<?php
//Fichier qui gère l'ensemble des formulaire POST
session_start();

include_once(getcwd() . "/config-db.php");
include_once(getcwd() . "/config-email.php");
include_once(getcwd() . "/config-recaptcha.php");
include_once(getcwd() . "/utils.php");
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

    case "upload":
        if (true || isset($_SESSION["LoggedIn"]) && $_SESSION['LoggedIn']) {
            $res = upload($connection);

            if ($res) {
                header("location:/ajout-ok");
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

    default:
        throw new Exception("ERROR_MISSING_ACTION - Action invalide - " . 'action' . ":'$action'");
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
                                    $password_salted_hashed = password_hash(hash('sha512', $passwd . $salt), PASSWORD_DEFAULT, ['cost' => 12]);
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
 * Upload des fichiers
 *
 * @return boolean          $res                -   Si l'opération s'est bien passée ou non
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

    if ($success) {
        for ($i=0; $i<$nbFiles-1; $i++) {
            $originalName = $_FILES["files"]["name"][$i];
            $content = file_get_contents($_FILES["files"]["tmp_name"][$i]);
            $size = $_FILES["files"]["size"][$i];

            $newFileName = createCryptedZipFile($connection, $originalName, $content, $size);
        }


        $passwd = $_SESSION["Data"]["password"];

        $content = unzipCryptedFile($connection, $newFileName, $passwd);

        $query = $connection->prepare("SELECT original_name FROM kioui_files WHERE path = ?");
        $query->bind_param("s", $newFileName);
        $query->execute();
        $result = $query->get_result();
        $query->close();
        $result = $result->fetch_assoc();
        $originalName = $result["original_name"];

        $filename = TEMP_DIR . $originalName;

        file_put_contents($filename, $content);

        downloadFile($filename, $originalName);

        unlink($filename);

    }

    return $res;
}

function downloadFile($file, $name, $mimeType='') {
    if (!is_readable($file)) {
        die("Fichier inaccessible !");
    }

    $size = filesize($file);
    $name = rawurldecode($name);

    $knownMimeTypes = array(
        "htm"  => "text/html",
        "exe"  => "application/octet-stream",
        "zip"  => "application/zip",
        "doc"  => "application/msword",
        "jpg"  => "image/jpg",
        "php"  => "text/plain",
        "xls"  => "application/vnd.ms-excel",
        "ppt"  => "application/vnd.ms-powerpoint",
        "gif"  => "image/gif",
        "pdf"  => "application/pdf",
        "txt"  => "text/plain",
        "html" => "text/html",
        "png"  => "image/png",
        "jpeg" => "image/jpg",
    );

    if ($mimeType == '') {
        $fileExtension = pathinfo($file, PATHINFO_EXTENSION);

        if (array_key_exists($fileExtension, $knownMimeTypes)) {
            $mime_type = $knownMimeTypes[$fileExtension];
        }
        else {
            $mimeType = "application/force-download";
        }
    }

    @ob_end_clean();
    header("Content-Type: " . $mimeType);
    header('Content-Disposition: attachment; filename="' . $name . '"');
    header("Content-Transfer-Encoding: binary");
    header("Accept-Ranges: bytes");

    header("Content-Length: " . $size);

    $chunksize = 1*(1024*1024);
    $bytes_send = 0;

    if ($file = fopen($file, 'r')) {
        while (!feof($file) && !connection_aborted() && $bytes_send<$size) {
            $buffer = fread($file, $chunksize);
            echo($buffer);
            flush();
            $bytes_send += strlen($buffer);
        }

        fclose($file);
    }
    else {
        die("Erreur : impossible d'ouvrir le fichier");
    }
}

function unzipCryptedFile($connection, $newFileName, $key) {
    $query = $connection->prepare("SELECT * FROM kioui_files WHERE path = ?");
    $query->bind_param("s", $newFileName);
    $query->execute();
    $result = $query->get_result();
    $query->close();
    $fileData = $result->fetch_assoc();

    $zipTextEncrypted = file_get_contents(TARGET_DIR.$newFileName);

    $zipText = decryptText($zipTextEncrypted, $key, $fileData["salt"], null);

    file_put_contents(TEMP_DIR.$newFileName, $zipText);

    $zipFile = new ZipArchive;

    if ($zipFile->open(TEMP_DIR.$newFileName) === true) {
        $zipFile->extractTo(TEMP_DIR."zip/");
        $zipFile->close();
    }

    $content = file_get_contents(TEMP_DIR."zip/".$newFileName);

    unlink(TEMP_DIR.$newFileName);
    unlink(TEMP_DIR."zip/".$newFileName);

    return $content;
}

function createCryptedZipFile($connection, $originalName, $content, $size) {
    $key = $_SESSION["Data"]["password"];
    $ownerId = $_SESSION["Data"]["id"];
    $ip = $_SERVER['REMOTE_ADDR'];

    $newFileName = randomString(SIZE_FILE_NAME);

    $zipFile = new ZipArchive;

    if ($zipFile->open(TARGET_DIR.$newFileName, ZipArchive::CREATE) === TRUE) {
        $zipFile->addFromString($newFileName, $content);
        $zipFile->close();
    }
    else {
        throw new Exception("Le zip n'a pas pu être créé");
    }

    $zipText = file_get_contents(TARGET_DIR.$newFileName);

    list($encryptedText, $salt, $hash) = encryptText($zipText, $key, null);

    file_put_contents(TARGET_DIR.$newFileName, $encryptedText);

    $query = $connection->prepare("INSERT INTO kioui_files (original_name, path, owner, salt, size, ip) VALUES (?,?,?,?,?,?)");
    $query->bind_param("ssisis", $originalName, $newFileName, $ownerId, $salt, $size, $ip);
    $query->execute();
    $query->close();

    return $newFileName;
}

?>
