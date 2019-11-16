<?php
//Fichier qui gère l'ensemble des formulaire POST
session_start();

include_once(getcwd() . "/config-db.php");
include_once(getcwd() . "/config-email.php");
include_once(getcwd() . "/config-recaptcha.php");
include_once(getcwd() . "/utils.php");
include_once(getcwd() . "/totp.php");
require_once(getcwd() . '/PHPMailer/PHPMailerAutoload.php');

$action = (isset($_POST['action'])) ? $_POST['action'] : "";
$action = ($action == "" && isset($_GET['action']) && $_GET['action'] != "") ? $_GET['action'] : $action;

switch ($action) {
    case "login":
        die(login($_POST['login_email'], $_POST['login_passwd'], $connection));
        break;
    case "register":
        die(register($_POST['register_username'], $_POST['register_email'], $_POST['register_passwd'], $_POST['register_passwd2'], $_POST['register_cgu'], $_POST['register_recaptchatoken'], $connection, $em, $recaptcha));
        break;
    case "verif-email":
        die(verifEmail($_GET['token'], $connection));
        break;
    case "change-password":
        die(changePassword($_SESSION['Data']['id'],$_POST['change-password_oldPassword'],$_POST['change-password_newPassword'],$connection));
        break;
    case "backup-key":
        die(backupKey($_POST['backup-key_key'], $connection));
        break;
    case "enable-totp":
        die(enableTOTP($_POST['enable-totp_key'], $_POST['enable-totp_code'], $connection));
        break;
    case "disable-totp":
        die(disableTOTP($_POST['enable-totp_code'], $connection));
        break;
    case "validate-totp":
        die(validateTOTP($_POST['validate-totp_code'], $connection));
        break;
    case "request-data":
        die(requestData($_POST['request-data_checked'], $_POST['request-data_passwd'], $connection));
        break;
    case "download-data":
        die(downloadData($connection, $_GET['download-data_file']));
        break;
    case "contact":
        die(contactForm($em, $_POST['contact-email'], $_POST['contact-subject'], $_POST['contact-message']));
        break;
    case "logout":
        session_destroy();
        header("Location: /");
        break;

    case "upload":
        if (isset($_SESSION["LoggedIn"]) && $_SESSION['LoggedIn']) {
            $res = upload($connection);

            if ($res) {
                header("location:/espace-utilisateur/accueil-utilisateur");
            }
            else {
                header("location:/ajout-nok");
            }
        }
        else {
            header("location:/ajout-nok");
        }

        die();

        break;

    case "download":
        if (isset($_SESSION["LoggedIn"]) && $_SESSION['LoggedIn']) {
            $fileName = (isset($_GET["filename"]) ? $_GET["filename"] : null);
            $fileKey = (isset($_GET["filekey"]) ? $_GET["filekey"] : null);

            if ($fileName === null || $fileKey === null) {
                echo "mauvais GET";
            }
            else {
                $res = downloadAction($connection, $fileName, $fileKey);

                if ($res) {
                    echo "<script>window.close();</script>";
                    header("location:/espace-utilisateur/accueil-utilisateur");
                }
            }
        }
        break;

    default:
        throw new Exception("ERROR_MISSING_ACTION#Action invalide - " . 'action' . ":'$action'");
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
        if (isset($userData['id']) && $userData['id'] != null && password_verify(hash('sha512', hash('sha512', $passwd . $userData['salt'])), $userData['password'])) {

            //Verification du compte
            if ($userData['access_level'] != "" && $userData['status'] == "ALIVE") {

                //Verification TOTP
                if ($userData['totp'] != "") {
                    $_SESSION['totp_validated'] = false;
                } else {
                    $_SESSION['totp_validated'] = true;
                }

                #Attribution des données de session
                $_SESSION['Data'] = $userData;
                $_SESSION['LoggedIn'] = true;
                $_SESSION['UserPassword'] = hash('sha512', $passwd . $userData['salt']);

                $result = "SUCCESS#Bienvenue " . $_SESSION['Data']['username'] . "#/espace-utilisateur/accueil";


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
            $result = "ERROR_INVALID_CREDENTIALS#Identifiants de connexion invalides.";
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
 * @param string            $recaptchatoken     -   Jeton recaptcha
 * @param mysqlconnection   $connection         -   Connexion BDD effectuée dans le fichier config-db.php
 * @param array             $em                 -   Identifiants email de config-email.php
 * @param array             $recaptcha          -   Identifiants recaptcha de config-recaptcha.php
 *
 * @return string
 */
function register($username, $email, $passwd, $passwd2, $cgu, $recaptchatoken, $connection, $em, $recaptcha) {

    $result = "ERROR_UNKNOWN#Une erreur est survenue.";

    //Verification des champs
    if (isset($username, $email, $passwd, $passwd2, $cgu, $recaptchatoken, $connection, $recaptcha) && $email != "" && $passwd != "" && $passwd2 != "" && $username != "" && $cgu == "on" && $recaptchatoken != "recdaptcha") {

        //Verification re-captcha
        $recaptcha_response = file_get_contents($recaptcha['url'] . '?secret=' . $recaptcha['private'] . '&response=' . $recaptchatoken);
        $recaptcha_response = json_decode($recaptcha_response);

        if ($recaptcha_response->success == true && $recaptcha_response->score >= $recaptcha['score_minimum']) {

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
                                    $password_salted_hashed = password_hash(hash('sha512', hash('sha512', $passwd . $salt)), PASSWORD_DEFAULT, ['cost' => 12]);
                                    $status = "REGISTRATION";
                                    $accesslevel = "USER";
                                    $ip = $_SERVER['REMOTE_ADDR'];
                                    $registrationDate = time();
                                    $emailToken = randomString(64);

                                    $query = $connection->prepare("INSERT INTO kioui_accounts (email, username, password, salt, access_level, status, ip, registration_date, email_token) VALUES (?,?,?,?,?,?,?,?,?)");
                                    $query->bind_param("sssssssis", $email, $username, $password_salted_hashed, $salt, $accesslevel, $status, $ip, $registrationDate, $emailToken);
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
            $result = "ERROR_FAILED_CAPTCHA#Captcha invalide. Veuillez réessayer ulterieurement ou contactez le support.";
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
 * Ajout d'une clé de secours au compte / la remplace si déjà existante
 *
 * @param string            $key                -   Clé de cryptage
 * @param mysqlconnection   $connection         -   Connexion BDD effectuée dans le fichier config-db.php
 *
 * @return string
 */
function backupKey($key, $connection) {

    $result = "ERROR_UNKNOWN#Une erreur est survenue.";

    if (isValidSession($connection)) {

        if (isset($key) && strlen($key) == 16) {

            $backup_password = encryptText($_SESSION['UserPassword'], $key, $_SESSION['Data']['salt'], $raw=false)[0];

            $query = $connection->prepare("UPDATE kioui_accounts SET backup_password = ? WHERE id = ?");
            $query->bind_param("si", $backup_password, $_SESSION['Data']['id']);
            $query->execute();
            $query->close();

            $result = "SUCCESS#Votre clé a été sauvegardée.#/espace-utilisateur/compte";
        }

    } else {
        $result = "ERROR_INVALID_SESSION#Votre session est invalide. Déconnectez vous puis reconnectez vous. Si le problème persiste contactez le support.";
    }

    return $result;
}


/**
 * Activation de la double authentification par application.
 * (Formulaire AJAX)
 *
 * @param string            $key                -   Clé TOTP
 * @param string            $code               -   Code TOTP pour activation
 * @param mysqlconnection   $connection         -   Connexion BDD effectuée dans le fichier config-db.php
 *
 * @return string
 */
function enableTOTP($key, $code, $connection) {
    $result = "ERROR_UNKNOWN#Une erreur est survenue.";


    if (isValidSession($connection)) {

        //TOTP désactivé ?
        if ($_SESSION['Data']['totp'] == "") {

            //Verification du code de confirmation
            $ga = new PHP_GoogleAuthenticator();
            if ($ga->verifyCode($key, $code) == 1) {

                $query = $connection->prepare("UPDATE kioui_accounts SET totp = ? WHERE id = ?");
                $query->bind_param("si", $key, $_SESSION['Data']['id']);
                $query->execute();
                $query->close();
                $result = "SUCCESS#Double authentification activée avec succès.#/espace-utilisateur/compte";
            } else {
                $result = "ERROR_TOTP_INVALID#Le code saisi est invalide.";
            }

        } else {
            $result = "ERROR_TOTP_ENABLED#Vous avez déjà la double authentification par application d'activé.";
        }

    } else {
        $result = "ERROR_INVALID_SESSION#Votre session est invalide. Déconnectez vous puis reconnectez vous. Si le problème persiste contactez le support.";
    }

    return $result;

}


/**
 * Désactivation de la double authentification par application.
 * (Formulaire AJAX)
 *
 * @param string            $code               -   Code TOTP pour désactivation
 * @param mysqlconnection   $connection         -   Connexion BDD effectuée dans le fichier config-db.php
 *
 * @return string
 */
function disableTOTP($code, $connection) {
    $result = "ERROR_UNKNOWN#Une erreur est survenue.";

    if (isValidSession($connection)) {

        //TOTP activé ?
        if ($_SESSION['Data']['totp'] != "") {

            //Verification du code de confirmation
            $ga = new PHP_GoogleAuthenticator();
            if ($ga->verifyCode($_SESSION['Data']['totp'], $code) == 1) {

                $newtotp = "";
                $query = $connection->prepare("UPDATE kioui_accounts SET totp = ? WHERE id = ?");
                $query->bind_param("si", $newtotp, $_SESSION['Data']['id']);
                $query->execute();
                $query->close();
                $result = "SUCCESS#Double authentification désactivée avec succès.#/espace-utilisateur/compte";
            } else {
                $result = "ERROR_TOTP_INVALID#Le code saisi est invalide.";
            }

        } else {
            $result = "ERROR_TOTP_DISABLED#Vous avez déjà la double authentification par application désactivé.";
        }

    } else {
        $result = "ERROR_INVALID_SESSION#Votre session est invalide. Déconnectez vous puis reconnectez vous. Si le problème persiste contactez le support.";
    }

    return $result;

}


/**
 * Verification de la double authentification par application.
 * (Formulaire AJAX)
 *
 * @param string            $code               -   Code TOTP pour verification
 * @param mysqlconnection   $connection         -   Connexion BDD effectuée dans le fichier config-db.php
 *
 * @return string
 */
function validateTOTP($code, $connection) {
    $result = "ERROR_UNKNOWN#Une erreur est survenue.";

    $ga = new PHP_GoogleAuthenticator();
    if ($_SESSION['Data']['totp'] == "" || $ga->verifyCode($_SESSION['Data']['totp'], $code) == 1) {
        $_SESSION['totp_validated'] = true;
        $result = "SUCCESS#Validation effectuée.#/espace-utilisateur/accueil";
    } else {
        $result = "ERROR_TOTP_INVALID#Le code saisi est invalide.";
    }

    return $result;

}


/**
 * Téléchargement des données de l'utilisateur
 * (Formulaire AJAX)
 *
 * @param string            $checked            -   Checkboxes qui indiquent ce que l'on doit telecharger
 * @param string            $passwd             -   Mot de passe de l'utilisateur
 * @param mysqlconnection   $connection         -   Connexion BDD effectuée dans le fichier config-db.php
 *
 * @return string
 */
function requestData($checked, $passwd, $connection) {
    $result = "ERROR_UNKNOWN#Une erreur est survenue.";

    if (isValidSession($connection)) {

        //Identifiants correct ?
        if (password_verify(hash('sha512', hash('sha512', $passwd . $_SESSION['Data']['salt'])), $_SESSION['Data']['password'])) {

            //Verification checkbox
            if (true) {

                try {

                    $query = $connection->prepare("SELECT * FROM kioui_accounts WHERE id = ?");
                    $query->bind_param("i", $_SESSION['Data']['id']);
                    $query->execute();
                    $result = $query->get_result();
                    $query->close();
                    $userData = $result->fetch_assoc();

                    $salt = randomString(16);
                    $file = "../../uploads/tmp/kioui-fr-user-data-fetch-" . $_SESSION['Data']['id'] . "-" . $salt . ".json";

                    $user_data_file = fopen($file, "w");
                    fwrite($user_data_file, json_encode($userData));
                    fclose($user_data_file);

                    $result = "SUCCESS#Téléchargement...#/dl-data/" . $salt;

                } catch (Exception $e) {
                    $result = "ERROR#" . $e->get_message();
                }


            } else {
                $result = "ERROR_INVALID_FIELDS#Veuillez cocher au moins une case.";
            }

        } else {
            $result = "ERROR_INVALID_CREDENTIALS#Mot de passe invalide.";
        }

    } else {
        $result = "ERROR_INVALID_SESSION#Votre session est invalide. Déconnectez vous puis reconnectez vous. Si le problème persiste contactez le support.";
    }

    return $result;
}
function downloadData($connection, $salt) {

    if (isValidSession($connection)) {

        $file = TEMP_DIR . "kioui-fr-user-data-fetch-" . $_SESSION['Data']['id'] . "-" . $salt . ".json";

        downloadFile($file);

        /*
        if (file_exists($file)) {

            header('Content-Description: File Transfer');
            header('Content-Type: application/octet-stream');
            header('Content-Disposition: attachment; filename="'.basename($file).'"');
            header('Expires: 0');
            header('Cache-Control: must-revalidate');
            header('Pragma: public');
            header('Content-Length: ' . filesize($file));
            readfile($file);

            unlink($file);
        } else {
            header("Location : /404");
        }
        header("Location : /403");
        */
    }

}


/**
 * Envoi d'un email par le formulaire de contact
 * (Formulaire AJAX)
 *
 * @param array             $em                 -   Identifiants du fichier config-email.php
 * @param string            $email              -   Email expediteur
 * @param string            $subject            -   Sujet du message
 * @param string            $message            -   Message
 *
 * @return void
 */
function contactForm($em, $email, $subject, $message) {
    $result = "ERROR_UNKNOWN#Une erreur est survenue.";

    if (isset($email, $subject, $message) && $subject != "" && $message != "" && filter_var($email, FILTER_VALIDATE_EMAIL)) {

        sendMailwosmtp($em['address'], "Nouveau message du formulaire de contact", "De : " . $email . "\nSujet : " . $subject . "\nMessage :\n" . $message);

        sendMail($em, $email, "Accusé de réception", "Accusé de réception", "Bonjour, votre message a bien été transmis, et nous répondrons dans les plus brefs délais.", "https://ki-oui.ythepaut.com/", "KI-OUI");

        $result = "SUCCESS#Votre message a été envoyé, vous allez recevoir une confirmation de réception par e-mail.#null";

    } else {
        $result = "ERROR_MISSING_FIELDS#Veuillez remplir tous les champs.";
    }

    return $result;
}


/**
 * Upload des fichiers
 *
 * @param   mysqlconnection $connection			- 	Connection à la base de données SQL
 *
 * @return  boolean         $res                -   Si l'opération s'est bien passée ou non
 */
function upload($connection) {
    $res = true;

    if (!isset($_FILES["files"]) || !isset($_SESSION["Data"]) || !isset($_SESSION["LoggedIn"])) {
        return false;
    }

    $nbFiles = count($_FILES["files"]["name"]);

    $success = true;

    $log = "";

    for ($i=0; $i<$nbFiles-1; $i++) {
        $log .= "Nom : "      . $_FILES["files"]["name"][$i]     . " <br />";
        $log .= "Type : "     . $_FILES["files"]["type"][$i]     . " <br />";
        $log .= "Tmp name : " . $_FILES["files"]["tmp_name"][$i] . " <br />";
        $log .= "Error : "    . $_FILES["files"]["error"][$i]    . "<br />";
        $log .= "Size : "     . $_FILES["files"]["size"][$i]     . "<br />";

        if ($_FILES["files"]["error"][$i] == UPLOAD_ERR_OK && is_uploaded_file($_FILES["files"]["tmp_name"][$i])) {
            $log .= "Content : ";
            $log .= file_get_contents($_FILES["files"]["tmp_name"][$i]);
            $log .= "<br />";
        }

        $log .= "<br />";

        if ($_FILES["files"]["size"][$i] > MAX_FILE_SIZE) {
            $success = false;
        }
    }

    if ($success) {
        $log .= "Success";
    }
    else {
        $log .= "Failure";
    }

    $log .= "<br /><br />";

    // echo $log;

    $res = $success;

    $password = $_SESSION["UserPassword"];

    if ($success) {
        for ($i=0; $i<$nbFiles-1; $i++) {
            $originalName = $_FILES["files"]["name"][$i];
            $content = file_get_contents($_FILES["files"]["tmp_name"][$i]);
            $size = $_FILES["files"]["size"][$i];

            $newFileName = createCryptedZipFile($connection, $content, $size, $password, $originalName);
        }

        /*
        $content = unzipCryptedFile($connection, $newFileName, $password);

        $query = $connection->prepare("SELECT original_name FROM kioui_files WHERE path = ?");
        $query->bind_param("s", $newFileName);
        $query->execute();
        $result = $query->get_result();
        $query->close();
        $result = $result->fetch_assoc();
        $originalName = $result["original_name"];

        downloadFile($content, $name = $originalName, $from_string = true);
        */
    }

    return $res;
}

function downloadAction($connection, $fileName, $fileKey) {
    $content = unzipCryptedFile($connection, $fileName, $fileKey);

    if ($content === null) {
        die("URL invalide");
    }

    $query = $connection->prepare("SELECT original_name FROM kioui_files WHERE path = ?");
    $query->bind_param("s", $fileName);
    $query->execute();
    $result = $query->get_result();
    $query->close();
    $result = $result->fetch_assoc();
    $originalName = $result["original_name"];

    downloadFile($content, $name = $originalName, $from_string = true);

    return true;
}


?>
