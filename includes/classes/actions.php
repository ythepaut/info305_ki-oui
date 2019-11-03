<?php
//Fichier qui gère l'ensemble des formulaire POST

include_once(getcwd() . "/config-db.php");
include_once(getcwd() . "/config-email.php");
include_once(getcwd() . "/utils.php");
require_once(getcwd() . '/PHPMailer/PHPMailerAutoload.php');

$action = (isset($_POST['action'])) ? $_POST['action'] : "";
$action = ($action == "" && isset($_GET['action']) && $_GET['action'] != "") ? $_GET['action'] : $action;

switch ($action) {
    case "login":
        die(login($_POST['login_email'], $_POST['login_passwd'], $connection));
        break;
    case "register":
        die(register($_POST['register_username'], $_POST['register_email'], $_POST['register_passwd'], $_POST['register_passwd2'], $_POST['register_cgu'], $connection, $em));
        break;
    case "verif-email":
        die(verifEmail($_GET['token'], $connection));
        break;

        case "upload":
        $res = upload();

        if ($res) {
            header("location:/ajout-ok");
        }
        else {
            header("location:/ajout-nok");
        }

        break;

    default:
        //throw new Exception("ERROR_MISSING_ACTION#Action invalide : " . '$action' . " = '$action'");
        break;
}




/**
 * Connexion de l'utilisateur : Methode e-mail + mot de passe
 * (Formulaire AJAX)
 *
 * @param string            $email              -   Adresse e-mail de l'utilisateur
 * @param string            $passwd             -   Mot de passe de l'utilisateur
 * @param mysqlconnection   $connection         -   Connexion BDD effectuée dans le fichier config-db.php
 *
 * @return string
 */
function login($email, $passwd, $connection) {

    $result = "ERROR_UNKNOWN#Une erreur est survenue.";

    //Verification des champs
    if (isset($email, $passwd, $connection) && $email != "" && $passwd != "") {

        //Recuperation des données
        $query = $connection->prepare("SELECT * FROM kioui_accounts WHERE email = ?");
        $query->bind_param("s", $email);
        $query->execute();
        $result = $query->get_result();
        $query->close();
        $userData = $result->fetch_assoc();

        //Identifiants correct ?
        if (isset($userData['id']) && $userData['id'] != null && password_verify(hash('sha512', $passwd . $userData['salt']), $userData['password'])) {

            //Verification du compte
            if ($userData['access_level'] != "" && $userData['status'] == "ALIVE") {

                #Attribution des données de session
                $_SESSION['Data'] = $userData;
                $_SESSION['LoggedIn'] = true;

                $result = "SUCCESS#Bienvenue " . $_SESSION['Data']['username'] . "#/espace-utilisateur";

            } else {

                switch ($userData['status']) {
                    case "SUSPENDED":
                        $result = "ERROR_ACCOUNT_SUSPENDED#Connexion impossible : Ce compte est suspendu.";
                        break;
                    case "REGISTRATION":
                        $result = "ERROR_ACCOUNT_UNVERIFIED#Veuillez faire vérifier votre adresse e-mail avant de vous connecter.";
                        break;
                    default:
                        $result = "ERROR_INVALID_ACCESSLEVEL#Connexion impossible : Niveau d'accès insuffisant.";
                        break;
                }

            }

        } else {
            $result = "ERROR_INVALID_CREDENTIALS#Identifiants de connexion invalides";
        }

    } else {
        $result = "ERROR_MISSING_FIELDS#Veuillez remplir tous les champs.";
    }

    return $result . "#<script>window.href.location = '/';</script>";
}

/**
 * Enregistrement d'un nouvel utilisateur
 * (Formulaire AJAX)
 *
 * @param string            $username           -   Nom d'utilisateur
 * @param string            $email              -   Adresse e-mail de l'utilisateur
 * @param string            $passwd             -   Mot de passe de l'utilisateur
 * @param string            $passwd2            -   Mot de passe de l'utilisateur (confirmation)
 * @param string            $cgu                -   Utilisateur a accepté les cgu
 * @param mysqlconnection   $connection         -   Connexion BDD effectuée dans le fichier config-db.php
 * @param array             $em                 -   Identifiants email de config-email.php
 *
 * @return string
 */
function register($username, $email, $passwd, $passwd2, $cgu, $connection, $em) {

    $result = "ERROR_UNKNOWN#Une erreur est survenue.";

    //Verification des champs
    if (isset($username, $email, $passwd, $passwd2, $cgu, $connection, $em) && $email != "" && $passwd != "" && $passwd2 != "" && $username != "" && $cgu == "on") {

        if (strlen($username) <= 16 && strlen($username) >= 3) {

            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {

                if (strlen($passwd) >= 8 && preg_match("#[0-9]+#", $passwd) && preg_match("#[a-zA-Z]+#", $passwd)) {

                    if ($passwd == $passwd2) {

                        //Verification données e-mail
                        $query = $connection->prepare("SELECT * FROM kioui_accounts WHERE email = ?");
                        $query->bind_param("s", $email);
                        $query->execute();
                        $result = $query->get_result();
                        $query->close();
                        $userData = $result->fetch_assoc();

                        if ($userData['id'] == "") {

                            //Verification données nom d'utilisateur
                            $query = $connection->prepare("SELECT * FROM kioui_accounts WHERE username = ?");
                            $query->bind_param("s", $username);
                            $query->execute();
                            $result = $query->get_result();
                            $query->close();
                            $userData = $result->fetch_assoc();

                            if ($userData['id'] == "") {

                                $salt = randomString(16);
                                $password_salted_hashed = password_hash(hash('sha512', $passwd . $salt), PASSWORD_DEFAULT, ['cost' => 12]);
                                $status = "REGISTRATION";
                                $accesslevel = "USER";
                                $ip = $_SERVER['REMOTE_ADDR'];
                                $registrationDate = time();
                                $emailToken = randomString(64);

                                $query = $connection->prepare("INSERT INTO kioui_accounts (email, username, password, salt, access_level, status, ip, registration_date, email_token) VALUES (?,?,?,?,?,?,?,?,?)");
                                $query->bind_param("sssssssi", $email, $username, $password_salted_hashed, $salt, $accesslevel, $status, $ip, $registrationDate, $emailToken);
                                $query->execute();

                                sendMail($em, $email, "Bienvenue sur KI-OUI. Verifiez votre e-mail.", "Bienvenue !", "Merci de vous être inscrit " . $username . ".<br />Veuillez confirmer votre adresse e-mail pour pouvoir commencer à utiliser nos services en cliquant sur le lien ci-dessous.<br /><br />Si vous n'êtes pas à l'origine de cette action, ignorez cet e-mail.", "https://ki-oui.ythepaut.com/verif-email/" . $emailToken, "Vérifier mon e-mail");

                                $result = "SUCCESS#Compte créé. Veuillez confirmer votre e-mail avant de vous connecter.#null";

                            } else {
                                $result = "ERROR_USER_USERNAME#Ce nom d'utilisateur est déjà utilisé.";
                            }

                        } else {
                            $result = "ERROR_USER_EMAIL#Cette adresse e-mail est déjà utilisée.";
                        }

                    } else {
                        $result = "ERROR_INVALID_PASSWD2#Les deux mots de passe doivent correspondre.";
                    }

                } else {
                    $result = "ERROR_INVALID_PASSWD#Votre mot de passe doit faire au moins 8 caractères, contenir au moins une lettre et un chiffre.";
                }

            } else {
                $result = "ERROR_INVALID_EMAIL#Veuillez saisir une adresse e-mail valide.";
            }

        } else {
            $result = "ERROR_INVALID_USERNAME#Votre nom d'utilisateur doit faire entre 3 et 16 caractères.";
        }

    } else {
        $result = "ERROR_MISSING_FIELDS#Veuillez remplir tous les champs.";
    }

    return $result . "#<script>window.href.location = '/';</script>";
}


/**
 * Verification d'email après enregistrement
 *
 * @param string            $token              -   Jeton de verification e-mail
 * @param mysqlconnection   $connection         -   Connexion BDD effectuée dans le fichier config-db.php
 *
 * @return void
 */
function verifEmail($token, $connection) {

    $query = $connection->prepare("SELECT * FROM kioui_accounts WHERE email_token = ?");
    $query->bind_param("s", $token);
    $query->execute();
    $result = $query->get_result();
    $query->close();
    $userData = $result->fetch_assoc();

    if ($userData['status'] == "REGISTRATION") {

        $newStatus = "ALIVE";

        $query = $connection->prepare("UPDATE kioui_accounts SET status = ? , email_token = NULL WHERE email_token = ?");
        $query->bind_param("ss", $newStatus, $token);
        $query->execute();
        $query->close();

    }

    header("Location: /");

}


/**
 * Upload des fichiers
 *
 * @return boolean                              -   Si l'opération s'est bien passée ou non
 */
function upload() {
    $TARGET_DIR = "../../uploads/";

    $files = $_FILES['files'];

    var_dump($files);

    for ($i=0; $i<count($files["name"])-1; $i++) {
        $txt = "";

        $txt .= "Nom : " . $files["name"][$i] . " <br />";
        $txt .= "Type : " . $files["type"][$i] . " <br />";
        $txt .= "Tmp name : " . $files["tmp_name"][$i] . " <br />";
        $txt .= "Error : " . $files["error"][$i] . "<br />";
        $txt .= "Size : " . $files["size"][$i] . "<br />";

        $file_name = $files["name"][$i]; // <-- Changer ici pour changer le nom

        $target_file = $TARGET_DIR . $file_name;

        $success = move_uploaded_file($files["tmp_name"][$i], $target_file);

        if ($success) {
            $txt .= "Success";
        }
        else {
            $txt .= "Failure";
        }

        $txt .= "<br />";

        echo $txt;
    }

    return true;
}

?>
