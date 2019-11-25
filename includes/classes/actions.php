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
        die(login($_POST['login_email'], $_POST['login_passwd'], $_POST['login_remember'], $connection, $em));
        break;
    case "register":
        die(register($_POST['register_username'], $_POST['register_email'], $_POST['register_passwd'], $_POST['register_passwd2'], $_POST['register_cgu'], $_POST['register_recaptchatoken'], $connection, $em, $recaptcha));
        break;
    case "verif-email":
        die(verifEmail($_GET['token'], $connection));
        break;
    case "change-email":
        die(changeEmailConfirmation($_POST['change-email_newEmail'], $_POST['change-email_password'], $connection, $em));    
        break;
    case "change-password":
        die(changePassword($_POST['change-password_oldPassword'], $_POST['change-password_newPassword'], $_POST['change-password_newPasswordBis'], $connection));
        break;
    case "forgot-pwd":
        die(forgotPassword($_POST['forgot-pwd_email'], $_POST['forgot-pwd_backup-key'], $_POST['forgot-pwd_new-passwd'], $_POST['forgot-pwd_new-passwd2'], $connection));
        break;
    case "change-username":
        die(changeUsername($_POST['change-username_newusername'],$_POST['change-username_password'], $connection));
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
    case "validate-tfa":
        die(validateTFA($_POST['validate-tfa_code'], $connection));
        break;
    case "resend-tfa":
        die(sendTFACode($em, $connection));
        break;
    case "delete-known-devices":
        die(deleteKnownDevices($connection));
        break;
    case "request-data":
        die(requestData($_POST['request-data_checkedData'], $_POST['request-data_checkedEnc'], $_POST['request-data_checkedDec'], $_POST['request-data_passwd'], $connection));
        break;
    case "download-data":
        die(downloadData($connection, $_GET['download-data_file'], $_GET['download-data_extension']));
        break;
    case "delete-account-procedure":
        die(deleteAccountProcedure($_POST['delete-account-procedure_passwd'], $connection, $em));
        break;
    case "contact":
        die(contactForm($em, $_POST['contact-email'], $_POST['contact-subject'], $_POST['contact-message']));
        break;
    case "create-ticket":
        die(createTicket($_POST['create-ticket_subject'], $_POST['create-ticket_message'], $connection, $em));
        break;
    case "respond-ticket":
        die(respondTicket($_POST['respond-ticket_id'], $_POST['respond-ticket_message'], $connection, $em));
        break;
    case "close-ticket":
        die(closeTicket($_GET['close-ticket_id'], $connection, $em));
        break;
    case "prior-ticket":
        die(priorTicket($_GET['close-ticket_id'], $_GET['close-ticket_prior'], $connection));
        break;
    case "logout":
        session_destroy();
        header("Location: /");
        break;

    case "upload-file":
        if (isset($_SESSION["LoggedIn"]) && $_SESSION['LoggedIn']) {
            $res = upload($connection);

            if ($res) {
                header("location:/espace-utilisateur/accueil-utilisateur");
            } else {
                echo "Erreur";
            }
        } else {
            echo "non connecté ?";
        }

        die();

        break;

    case "download-file":
        if (isset($_POST["filename"]) && $_POST["filename"] != "") {
            $fileName = $_POST["filename"];
        } else {
            $fileName = null;
        }

        if (isset($_POST["filekey"]) && $_POST["filekey"] != "") {
            $fileKey = $_POST["filekey"];
        } else {
            $fileKey = null;
        }

        if ($fileName === null) {
            $_SESSION["error"] = "Pas de nom de fichier";
        } elseif ($fileKey === null) {
            $_SESSION["error"] = "Pas de clé de fichier";
        } else {
            $_SESSION["error"] = downloadAction($connection, $fileName, $fileKey);
        }

        header("location:/share-file");

        break;

    case "delete":
        die(deleteFile($_POST['delete-fileid'], $connection));
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
 * @param string            $remember (opt.)    -   Valeur de la checkbox "se souvenir de moi"
 * @param mysqlconnection   $connection         -   Connexion BDD effectuée dans le fichier config-db.php
 * @param array             $em                 -   Identifiants email dans le fichier config-email.php
 *
 * @return string
 */
function login($email, $passwd, $remember = "off", $connection, $em) {
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
            if ($userData['access_level'] != "" && ($userData['status'] == "ALIVE" || $userData['status'] == "DELETE_PROCEDURE")) {


                if ($userData['status'] == "DELETE_PROCEDURE") { //Annulation procedure de suppression

                    $newStatus = "ALIVE";
                    $newExpire = 0;

                    $query = $connection->prepare("UPDATE kioui_accounts SET status = ? , account_expire = ? WHERE id = ?");
                    $query->bind_param("sii", $newStatus, $newExpire, $userData['id']);
                    $query->execute();
                    $query->close();

                    sendMail($em, $_SESSION['Data']['email'], "Suppression de votre compte", "ANNULATION DE LA PROCEDURE DE SUPPRESSION", "Bonjour " . $_SESSION['Data']['username'] . ".<br />Suite à votre connexion, la procédure de suppression de votre compte a été annulée.", "https://ki-oui.ythepaut.com/", "KI-OUI");

                }


                //Verification TOTP
                $_SESSION['tfa'] = ($userData['totp'] != "") ? "totp" : "trusted";

                //Verification appareil connu ?
                $knownDevices = json_decode($userData['known_devices'], true);
                $thisDevice = array("hostname" => gethostbyaddr($_SERVER['REMOTE_ADDR']), "ip" => $_SERVER['REMOTE_ADDR'], "useragent" => $_SERVER["HTTP_USER_AGENT"]);
                $known = false;
                foreach ($knownDevices as $device) {
                    if ($device == $thisDevice) {
                        $known = true;
                    }
                }
                $_SESSION['tfa'] = ($_SESSION['tfa'] == "trusted" && !$known) ? "new_device" : $_SESSION['tfa'];


                #Attribution des données de session
                $_SESSION['Data'] = $userData;
                $_SESSION['LoggedIn'] = true;
                $_SESSION['UserPassword'] = hash('sha512', $passwd . $userData['salt']);

                $result = "SUCCESS#Bienvenue " . $_SESSION['Data']['username'] . "#/espace-utilisateur/accueil";

                if ($_SESSION['tfa'] == "new_device") {
                    sendTFACode($em, $connection);
                }

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
function sendTFACode($em, $connection) {
    $result = "ERROR_UNKNOWN#Une erreur est survenue.";
    if ($_SESSION['tfa'] == "new_device") {

        $characters = "0123456789";
        $charactersLength = strlen($characters);
        $randomString = "";
        for ($i = 0; $i < 6; $i++) {
            $randomString .= $characters[random_int(0, $charactersLength - 1)];
        }
        $expire = time() + 300;


        $query = $connection->prepare("UPDATE kioui_accounts SET tfa_code = ? , tfa_expire = ? WHERE id = ?");
        $query->bind_param("iii", $randomString, $expire, $_SESSION['Data']['id']);
        $query->execute();
        $query->close();

        sendMail($em, $_SESSION['Data']['email'], "Votre code de verification KI-OUI", $randomString, "Nous avons détecté une nouvelle connexion d'un appareil inconnu. Saisissez le code ci-dessus pour completer votre connexion.<br /><br />Si vous n'êtes pas à l'origine de cette requete, changez votre mot de passe et contactez le support.", "https://ki-oui.ythepaut.com/", "KI-OUI");

        $result = "SUCCESS#Un autre e-mail contenant votre code a été envoyé.#null";
    }

    return $result;
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
                                    $device = array(array("hostname" => gethostbyaddr($_SERVER['REMOTE_ADDR']), "ip" => $_SERVER['REMOTE_ADDR'], "useragent" => $_SERVER["HTTP_USER_AGENT"]));
                                    $firstDevice = json_encode($device);

                                    $query = $connection->prepare("INSERT INTO kioui_accounts (email, username, password, salt, access_level, status, ip, registration_date, email_token, known_devices) VALUES (?,?,?,?,?,?,?,?,?,?)");
                                    $query->bind_param("sssssssiss", $email, $username, $password_salted_hashed, $salt, $accesslevel, $status, $ip, $registrationDate, $emailToken, $firstDevice);
                                    $query->execute();
                                    $query->close();

                                    sendMail($em, $email, "Bienvenue sur KI-OUI. Verifiez votre e-mail.", "Bienvenue !", "Merci de vous être inscrit " . $username . ".<br />Veuillez confirmer votre adresse e-mail pour pouvoir commencer à utiliser nos services en cliquant sur le lien ci-dessous.<br /><br />Si vous n'êtes pas à l'origine de cette action, ignorez cet e-mail.", "https://ki-oui.ythepaut.com/verif-email/" . $emailToken, "Vérifier mon e-mail");

                                    $result = "SUCCESS#Compte créé. Veuillez confirmer votre e-mail avant de vous connecter.#null";

                                } else {
                                    $result = "ERROR_USED_USERNAME#Ce nom d'utilisateur est déjà utilisé.";
                                }

                            } else {
                                $result = "ERROR_USED_EMAIL#Cette adresse e-mail est déjà utilisée.";
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

            $backup_password = encryptText($_SESSION['UserPassword'], $key, $_SESSION['Data']['salt'], null, false)[0];

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
        $_SESSION['tfa'] = "trusted";

        $userDevices = json_decode($_SESSION['Data']['known_devices']);

        $device = array("hostname" => gethostbyaddr($_SERVER['REMOTE_ADDR']), "ip" => $_SERVER['REMOTE_ADDR'], "useragent" => $_SERVER["HTTP_USER_AGENT"]);
        array_push($userDevices, $device);

        $newDevices = json_encode($userDevices);

        $query = $connection->prepare("UPDATE kioui_accounts SET known_devices = ? , tfa_code = 0 , tfa_expire = 0 WHERE id = ?");
        $query->bind_param("si", $newDevices, $_SESSION['Data']['id']);
        $query->execute();
        $query->close();

        $result = "SUCCESS#Validation effectuée.#/espace-utilisateur/accueil";
    } else {
        $result = "ERROR_TOTP_INVALID#Le code saisi est invalide.";
    }

    return $result;

}


/**
 * Verification de la double authentification par email.
 * (Formulaire AJAX)
 *
 * @param string            $code               -   Code TFA pour verification
 * @param mysqlconnection   $connection         -   Connexion BDD effectuée dans le fichier config-db.php
 *
 * @return string
 */
function validateTFA($code, $connection) {
    $result = "ERROR_UNKNOWN#Une erreur est survenue.";

    refreshSession($connection);

    if ($_SESSION['Data']['tfa_code'] == $code && $_SESSION['Data']['tfa_expire'] > time()) {
        $_SESSION['tfa'] = "trusted";

        $userDevices = json_decode($_SESSION['Data']['known_devices']);

        $device = array("hostname" => gethostbyaddr($_SERVER['REMOTE_ADDR']), "ip" => $_SERVER['REMOTE_ADDR'], "useragent" => $_SERVER["HTTP_USER_AGENT"]);
        array_push($userDevices, $device);

        $newDevices = json_encode($userDevices);

        $query = $connection->prepare("UPDATE kioui_accounts SET known_devices = ? , tfa_code = 0 , tfa_expire = 0 WHERE id = ?");
        $query->bind_param("si", $newDevices, $_SESSION['Data']['id']);
        $query->execute();
        $query->close();

        $result = "SUCCESS#Validation effectuée.#/espace-utilisateur/accueil";
    } else {
        $result = "ERROR_INVALID_TFACODE#Ce code est invalide ou expiré (>5min).";
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
function requestData($dlData, $dlEnc, $dlDec, $passwd, $connection) {
    $result = "ERROR_UNKNOWN#Une erreur est survenue.";

    if (isValidSession($connection)) {

        //Identifiants correct ?
        if (password_verify(hash('sha512', hash('sha512', $passwd . $_SESSION['Data']['salt'])), $_SESSION['Data']['password'])) {

            //Verification checkbox
            if ($dlData == "on" || $dlEnc == "on" || $dlDec == "on") {

                $zip = new ZipArchive;
                $salt = randomString(8);
                if ($zip->open(TEMP_DIR . "kioui-fr-user-data-fetch-" . $_SESSION['Data']['id'] . "-" . $salt . ".zip", ZipArchive::CREATE) === TRUE) {

                    $filesToDelete = array();

                    //Telechargement ligne utilisateur dans bdd
                    if ($dlData == "on") {

                        //Recuperation des données
                        $query = $connection->prepare("SELECT * FROM kioui_accounts WHERE id = ?");
                        $query->bind_param("i", $_SESSION['Data']['id']);
                        $query->execute();
                        $result = $query->get_result();
                        $query->close();
                        $userData = $result->fetch_assoc();

                        //Ajout du fichier dans l'archive
                        $zip->addFromString('data.json', json_encode($userData, JSON_PRETTY_PRINT));

                    }

                    //Telechargement des fichiers cryptés
                    if ($dlEnc == "on") {

                        //Recuperation des fichiers
                        $files = getFiles($_SESSION['Data']['id'], $connection);

                        foreach ($files as $file) {
                            $zip->addFile(UPLOAD_DIR . $file['path'], 'encrypted/' . $file['path']);
                        }

                    }

                    //Telechargement des fichiers décryptés
                    if ($dlDec == "on") {

                        //Recuperation des fichiers
                        $files = getFiles($_SESSION['Data']['id'], $connection);

                        foreach ($files as $file) {
                            list($content, $name) = unzipCryptedFile($connection, $file['path'], $_SESSION['UserPassword']);
                            $tmpFile = TEMP_DIR . "kioui-fr-user-data-fetch-" . $_SESSION['Data']['id'] . "-" . $salt . "-" . $name;
                            file_put_contents($tmpFile, $content);
                            $zip->addFile($tmpFile, 'unencrypted/' . $name);
                            array_push($filesToDelete, $tmpFile);
                        }

                    }

                    $zip->addFromString('lisez-moi.txt', "KI-OUI - Données personnelles\n\nLes données contenues dans cette archive doivent rester secretes.");
                    $zip->close();

                    //Suppression des fichiers temporaires
                    foreach ($filesToDelete as $file) {
                        unlink($file);
                    }

                }

                $result = "SUCCESS#Téléchargement...#/dl-data/" . $salt . "/" . "zip";

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
function downloadData($connection, $salt, $extension) {

    if (isValidSession($connection)) {

        $file = TEMP_DIR . "kioui-fr-user-data-fetch-" . $_SESSION['Data']['id'] . "-" . $salt . "." . $extension;

        downloadFile($file);
        unlink($file);
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
 * Fonction qui change le nom d'un utilisateur
 *
 * @param   mysqlconnection $connection         -   Connection à la base de données SQL
 * @param   string          $newUsername        -   le nouveau nom de l'utilisateur
 * @param   string          $password           -   le mot de passe de l'utilisateur
 *
 * @return  string
 */
function changeUsername($newUsername, $password, $connection){

    $result="ERROR_UNKNOWN#Une erreur est survenue.";

    if (isValidSession($connection)) {
        if (isset($newUsername, $password) && $newUsername != "" && $password != "") {
            //vérification du mdp
            $query = $connection->prepare("SELECT * FROM kioui_accounts WHERE id = ?");
            $query->bind_param("i", $_SESSION['Data']['id']);
            $query->execute();
            $result = $query->get_result();
            $query->close();
            $userData = $result->fetch_assoc();
            if (isset($userData['id']) && $userData['id'] != null && password_verify(hash('sha512', hash('sha512', $password . $userData['salt'])), $userData['password'])) {
                if (strlen($newUsername) <= 16 && strlen($newUsername) >= 3) {
                    //Verification données nom d'utilisateur
                    $query = $connection->prepare("SELECT * FROM kioui_accounts WHERE username = ?");
                    $query->bind_param("s", $newUsername);
                    $query->execute();
                    $result = $query->get_result();
                    $query->close();
                    $userData = $result->fetch_assoc();
                    if ($userData['id'] == "") {
                        //changement nom d'utilisateur bdd
                        $query = $connection->prepare("UPDATE kioui_accounts SET username = ? WHERE kioui_accounts.id = ?");
                        $query->bind_param("si", $newUsername, $_SESSION['Data']['id']);
                        $query->execute();
                        $query->close();

                        $result = "SUCCESS#Votre nom d'utilisateur a bien été changé#/espace-utilisateur/compte";
                    } else {
                        $result = "ERROR_USED_USERNAME#Ce nom d'utilisateur est déjà utilisé.";
                    }
                } else {
                    $result = "ERROR_INVALID_USERNAME#Votre nom d'utilisateur doit faire entre 3 et 16 caractères.";
                }
            } else {
                $result = "ERROR_INVALID_CREDENTIALS#Mot de passe invalide.";
            }
        } else {
            $result = "ERROR_MISSING_FIELDS#Veuillez remplir tous les champs.";
        }
    } else {
        $result = "ERROR_INVALID_SESSION#Votre session est invalide. Déconnectez vous puis reconnectez vous. Si le problème persiste contactez le support.";
    }

    return $result;
}


/**
 * Upload des fichiers
 *
 * @param   mysqlconnection $connection            -     Connection à la base de données SQL
 *
 * @return  boolean         $res                -   Si l'opération s'est bien passée ou non
 */
function upload($connection) {
    $res = false;

    if (!isset($_FILES["files"]) || !isset($_SESSION["Data"]) || !isset($_SESSION["LoggedIn"])) {
        return false;
    }

    $nbFiles = count($_FILES["files"]["name"]);

    $totalSize = 0;

    for ($i=0; $i<$nbFiles-1; $i++) {
        if ($_FILES["files"]["error"][$i] == UPLOAD_ERR_OK && is_uploaded_file($_FILES["files"]["tmp_name"][$i])) {
            $totalSize += $_FILES["files"]["size"][$i];
        }
    }

    $maxSize = $_SESSION["Data"]["quota"];
    $usedSpace = getSize($_SESSION["Data"]["id"], $connection);

    if ($totalSize > $maxSize - $usedSpace) {
        $res = false;
    }
    else {
        $password = $_SESSION["UserPassword"];

        for ($i=0; $i<$nbFiles-1; $i++) {
            if ($_FILES["files"]["error"][$i] == UPLOAD_ERR_OK && is_uploaded_file($_FILES["files"]["tmp_name"][$i])) {
                $originalName = $_FILES["files"]["name"][$i];
                $content = file_get_contents($_FILES["files"]["tmp_name"][$i]);
                $size = $_FILES["files"]["size"][$i];

                $newFileName = createCryptedZipFile($connection, $content, $size, $password, $originalName);
            }
        }

        $res = true;
    }

    return $res;
}


function downloadAction($connection, $fileName, $fileKey) {
    list($content, $originalName) = unzipCryptedFile($connection, $fileName, $fileKey);

    if ($content === null) {
        return "Clé invalide";
    }

    downloadFile($content, $name = $originalName, $from_string = true);

    return "ok";
}


/**
 * Suppression d'un fichier
 * (Formulaire AJAX)
 *
 * @param   mysqlconnection $connection         -   Connection à la base de données SQL
 * @param   integer         $fileId             -   Id du fichier à supprimer
 *
 * @return  string
 */
function deleteFile($fileId, $connection) {
    $result = "ERROR_UNKNOWN#Une erreur est survenue.";

    if (isValidSession($connection)) {
        if (isset($fileId)) {
            // acquisition du fichier crypté
            $query = $connection->prepare("SELECT * FROM kioui_files WHERE id = ? ");
            $query->bind_param("i", $fileId);
            $query->execute();
            $res = $query->get_result();
            $query->close();
            $fileData = $res->fetch_assoc();

            $filePath = $fileData['path'];
            $fileOwner = $fileData['owner'];

            if (isset($filePath) && $filePath != "" && $fileOwner == $_SESSION['Data']['id']) {
                /*
                // suppression dans le répertoire
                if (unlink(UPLOAD_DIR.$filePath)) {

                        // suppression dans la BDD
                        $query = $connection->prepare("DELETE FROM kioui_files WHERE id = ? ");
                        $query->bind_param("i", $fileId);
                        $query->execute();
                        $query->close();

                        $result = "SUCCESS#Fichier supprimé avec succès.#/espace-utilisateur/accueil";

                    } else {
                        $result = "ERROR_NOT_DELETED#Suppression du fichier impossible.";
                    }

                } else {
                    $result = "ERROR_DONT_EXIST#Fichier inexistant.";
                }
                else {$result = "ERROR_NOT_DELETED#Suppression du fichier impossible.";}
                */

                // suppression dans le répertoire
                $deleted = unlink(UPLOAD_DIR.$filePath);

                $query = $connection->prepare("DELETE FROM kioui_files WHERE id = ? ");
                $query->bind_param("i", $fileId);
                $query->execute();
                $query->close();

                if ($deleted) {
                    $result = "SUCCESS#Fichier supprimé avec succès.#/espace-utilisateur/accueil";
                } else {
                    $result = "WARNING_FILE_DOESNT_EXIST#Le fichier n'existe pas sur le disque.#/espace-utilisateur/accueil";
                }
            }
        }
    } else {
        $result = "ERROR_INVALID_SESSION#Votre session est invalide. Déconnectez vous puis reconnectez vous. Si le problème persiste contactez le support.";
    }

    return $result;
}


/**
 * Suppression des appareils enregistrés
 * (Formulaire AJAX)
 *
 * @param   mysqlconnection $connection         -   Connection à la base de données SQL
 *
 * @return  string
 */
function deleteKnownDevices($connection) {

    $newVal = "[]";
    $query = $connection->prepare("UPDATE kioui_accounts SET known_devices = ? WHERE id = ?");
    $query->bind_param("si", $newVal, $_SESSION['Data']['id']);
    $query->execute();
    $query->close();

    return "SUCCESS#Appareils enregistrés supprimés avec succès.#/espace-utilisateur/compte";
}


/**
 * Fonction qui change le mot de passe si oublié
 * (Formulaire AJAX)
 *
 * @param string            $email              -   Adresse e-mail de l'utilisateur
 * @param string            $backupKey          -   Clé de secours de l'utilisateur
 * @param string            $passwd             -   Nouveau mot de passe de l'utilisateur
 * @param string            $passwd2            -   Nouveau mot de passe de l'utilisateur (confirmation)
 * @param mysqlconnection   $connection         -   Connexion BDD effectuée dans le fichier config-db.php
 *
 * @return  string
 */
function forgotPassword($email, $backupKey, $passwd, $passwd2, $connection) {

    $result = "ERROR_UNKNOWN#Une erreur est survenue.";

    if (isset($email, $backupKey, $passwd, $passwd2) && $email != "" && $backupKey != "" && $passwd != "") {

        if (strlen($passwd) >= 8 && preg_match("#[0-9]+#", $passwd) && preg_match("#[a-zA-Z]+#", $passwd)) {

            if ($passwd == $passwd2) {

                //Recuperation des données utilisateur
                $query = $connection->prepare("SELECT * FROM kioui_accounts WHERE email = ?");
                $query->bind_param("s", $email);
                $query->execute();
                $result = $query->get_result();
                $query->close();
                $userData = $result->fetch_assoc();

                if ($userData['backup_password'] != "") {

                    //Tentative de decryptage du mot de passe
                    $success = false;
                    $oldPassword = "";

                    try {
                        $oldPassword = openssl_decrypt(base64_decode($userData['backup_password']), AES_METHOD, $backupKey . $userData['salt'], OPENSSL_RAW_DATA, substr($userData['salt'], 0, 16));
                        $success = true;
                    } catch (Exception $ex) {
                        $success = false;
                    }
                    if ($success && $oldPassword != "") {
                        //Décrypter et rencrypter tous les fichiers
                        $files = getFiles($userData['id'], $connection);
                        //Obtenir la clés de décryptage et de cryptage
                        $oldUserKey = $oldPassword;
                        $newUserKey = hash('sha512', $passwd . $userData['salt']);

                        foreach ($files as $file) {
                            list($content, $name) = unzipCryptedFile($connection, $file['path'], $oldUserKey);
                            createCryptedZipFile($connection, $content, $file['size'], $newUserKey, $name);
                            //Suppresion du fichier
                            deleteFile($file['id'], $connection);
                        }

                        $new_password_salted_hashed = password_hash(hash('sha512', hash('sha512', $passwd . $userData['salt'])), PASSWORD_DEFAULT, ['cost' => 12]);

                        $bckppwd = "";

                        $query = $connection->prepare("UPDATE kioui_accounts SET password = ? , backup_password = ? WHERE id = ?");
                        $query->bind_param("ssi", $new_password_salted_hashed, $bckppwd, $userData['id']);
                        $query->execute();
                        $query->close();

                        $result = "SUCCESS#Votre mot de passe a bien été modifié, et votre clé réinitialisé.#null";

                    } else {
                        $result = "ERROR_INVALID_EMAIL_OR_KEY#L'adresse e-mail ou la clé de secours est invalide.";
                    }

                } else {
                    $result = "ERROR_INVALID_EMAIL_OR_KEY#L'adresse e-mail ou la clé de secours est invalide.";
                }

            } else {
                $result = "ERROR_INVALID_PASSWD2#Les deux mots de passe doivent correspondre.";
            }

        } else {
            $result = "ERROR_INVALID_PASSWD#Votre mot de passe doit faire au moins 8 caractères, contenir au moins une lettre et un chiffre.";
        }

    } else {
        $result = "ERROR_MISSING_FIELDS#Veuillez remplir tous les champs.";
    }


    return $result;

}


/**
 * Fonction qui change le mot de passe d'un utilisateur donné
 * (Formulaire AJAX)
 *
 * @param string              $oldPassword          - Ancien mot de passe
 * @param string              $newPassword          - Nouveau mot de passe
 * @param string              $newPasswordBis       - Confirmation du nouveau mot de passe
 * @param mysqlconnection     $connection           - Connexion BDD effectuée dans le fichier config-db.php
 *
 * @return string
*/
function changePassword($oldPassword, $newPassword, $newPasswordBis, $connection) {

    $result = "ERROR_UNKNOWN#Une erreur est survenue.";

    if (isValidSession($connection)) {

        if (isset($oldPassword, $newPassword) && $oldPassword != "" && $newPassword != "") {

            //Recuperation des données
            $query = $connection->prepare("SELECT * FROM kioui_accounts WHERE id = ?");
            $query->bind_param("i", $_SESSION['Data']['id']);
            $query->execute();
            $result = $query->get_result();
            $query->close();
            $userData = $result->fetch_assoc();

            //Identifiants correct ?
            if (isset($userData['id']) && $userData['id'] != null && password_verify(hash('sha512', hash('sha512', $oldPassword . $userData['salt'])), $userData['password'])) {

                //Nouveau mot de passe correct ?
                if (strlen($newPassword) >= 8 && preg_match("#[0-9]+#", $newPassword) && preg_match("#[a-zA-Z]+#", $newPassword)) {

                    if ($newPassword == $newPasswordBis) {

                        //Décrypter et rencrypter tous les fichiers
                        $files = getFiles($userData['id'], $connection);
                        //Obtenir la clés de décryptage et de cryptage
                        $oldUserKey = hash('sha512', $oldPassword . $userData['salt']);
                        $newUserKey = hash('sha512', $newPassword . $userData['salt']);

                        foreach ($files as $file) {
                            list($content, $name) = unzipCryptedFile($connection, $file['path'], $oldUserKey);
                            createCryptedZipFile($connection, $content, $file['size'], $newUserKey, $name);
                            //Suppresion du fichier
                            deleteFile($file['id'], $connection);
                        }

                        //Obtention MDP a insérer
                        $new_password_salted_hashed = password_hash(hash('sha512', hash('sha512', $newPassword . $userData['salt'])), PASSWORD_DEFAULT, ['cost' => 12]);
                        //Changement MDP DBB
                        $query = $connection->prepare("UPDATE kioui_accounts SET password = ? WHERE id = ?");
                        $query->bind_param("si", $new_password_salted_hashed, $userData['id']);
                        $query->execute();
                        $query->close();

                        //Mise à jour de la session
                        $_SESSION['UserPassword'] = hash('sha512', $newPassword . $userData['salt']);

                        $result = "SUCCESS#Votre mot de passe a été modifié avec succès.#/espace-utilisateur/compte";

                    } else {
                        $result = "ERROR_INVALID_PASSWD2#Les deux mots de passe doivent correspondre.";
                    }

                } else {
                    $result = "ERROR_INVALID_PASSWD#Votre mot de passe doit faire au moins 8 caractères, contenir au moins une lettre et un chiffre.";
                }

            } else {
                $result = "ERROR_INVALID_CREDENTIALS#Mot de passe invalide.";
            }

        } else {
            $result = "ERROR_MISSING_FIELDS#Veuillez remplir tous les champs.";
        }

    } else {
        $result = "ERROR_INVALID_SESSION#Votre session est invalide. Déconnectez vous puis reconnectez vous. Si le problème persiste contactez le support.";
    }

    return $result;
}


/**
 * Fonction qui démarre la procedure de suppression de compte
 * (Formulaire AJAX)
 *
 * @param string              $passwd               - Mot de passe de l'utilisateur
 * @param mysqlconnection     $connection           - Connexion BDD effectuée dans le fichier config-db.php
 * @param array               $em                   -   Identifiants email dans le fichier config-email.php
 *
 * @return string
*/
function deleteAccountProcedure($passwd, $connection, $em) {

    $result = "ERROR_UNKNOWN#Une erreur est survenue.";

    if (isValidSession($connection)) {

        if (password_verify(hash('sha512', hash('sha512', $passwd . $_SESSION['Data']['salt'])), $_SESSION['Data']['password'])) {

            if ($_SESSION['Data']['access_level'] != "ADMINISTRATOR") {

                $newStatus = "DELETE_PROCEDURE";
                $expire = time() + 3600*24*15; //J+15

                $query = $connection->prepare("UPDATE kioui_accounts SET status = ? , account_expire = ? WHERE id = ?");
                $query->bind_param("sii", $newStatus, $expire, $_SESSION['Data']['id']);
                $query->execute();
                $query->close();

                sendMail($em, $_SESSION['Data']['email'], "Suppression de votre compte", "LANCEMENT DE LA PROCEDURE DE SUPPRESSION", "Bonjour " . $_SESSION['Data']['username'] . ".<br />Suite à votre demande, la procédure de suppression de votre compte a débuté. Votre compte et vos données seront supprimmés et irrécuperables dans 15 jours.<br />Pour annuler la procedure, reconnectez-vous avant le " . date("d/m/Y H:m", $expire) . ".<br /><br />Si vous n'êtes pas à l'origine de cette action, reconnectez-vous à votre espace, changez votre mot de passe et contactez le support.", "https://ki-oui.ythepaut.com/espace-utilisateur/compte", "Annuler la procedure");

                session_destroy();

                $result = "SUCCESS#Procedure de suppression lancée.#/";

            } else {
                $result = "ERROR_ACCESSLEVEL_TOOHIGH#Votre niveau d'accès ne vous permet pas de clôturer votre compte : Vous êtes Administrateur.";
            }

        } else {
            $result = "ERROR_INVALID_CREDENTIALS#Mot de passe invalide.";
        }

    } else {
        $result = "ERROR_INVALID_SESSION#Votre session est invalide. Déconnectez vous puis reconnectez vous. Si le problème persiste contactez le support.";
    }

    return $result;
}


/**
 * Fonction qui crée un ticket de support
 * (Formulaire AJAX)
 *
 * @param string              $subject              - Sujet du ticket
 * @param string              $message              - Description du ticket
 * @param mysqlconnection     $connection           - Connexion BDD effectuée dans le fichier config-db.php
 * @param array               $em                   -   Identifiants email dans le fichier config-email.php
 *
 * @return string
*/
function createTicket($subject, $message, $connection, $em) {

    $result = "ERROR_UNKNOWN#Une erreur est survenue.";

    if (isValidSession($connection)) {

        if (isset($subject, $message) && strlen($subject) > 3 && strlen($message) > 3 && strlen($subject) < 200) {

            $ticketStatus = "OPEN";
            $ticketPriority = "MEDIUM";
            $ticketAssigned = 0;
            $date = time();

            $conversationArray = array(
                array(
                    "senderRole" => "USER",
                    "senderName" => $_SESSION['Data']['username'],
                    "date" => time(),
                    "message" => $message
                )
            );

            $conversation = json_encode($conversationArray);

            $query = $connection->prepare("INSERT INTO kioui_tickets (user, subject, status, priority, assigned, conversation, date) VALUES (?,?,?,?,?,?,?)");
            $query->bind_param("isssisi", $_SESSION['Data']['id'], $subject, $ticketStatus, $ticketPriority, $ticketAssigned, $conversation, $date);
            $query->execute();
            $query->close();

            sendMail($em, $_SESSION['Data']['email'], "Demande support", "DEMANDE CRÉÉE", "Bonjour " . $_SESSION['Data']['username'] . ".<br />Votre demande de support « " . $subject . " » a bien été créée.<br />Nous vous invitons à consulter votre demande par le lien ci-dessous. Nous vous répondrons dans les plus brefs délais.", "https://ki-oui.ythepaut.com/espace-utilisateur/assistance", "Assistance");

            $result = "SUCCESS#Demande de support créée.#/espace-utilisateur/assistance";

        } else {
            $result = "ERROR_MISSING_FIELDS#Veuillez remplir tous les champs.";
        }

    } else {
        $result = "ERROR_INVALID_SESSION#Votre session est invalide. Déconnectez vous puis reconnectez vous. Si le problème persiste contactez le support.";
    }

    return $result;
}


/**
 * Fonction qui crée un ticket de support
 * (Formulaire AJAX)
 *
 * @param string              $id                   - ID du ticket
 * @param string              $message              - Message de reponse
 * @param mysqlconnection     $connection           - Connexion BDD effectuée dans le fichier config-db.php
 * @param array               $em                   -   Identifiants email dans le fichier config-email.php
 *
 * @return string
*/
function respondTicket($id, $message, $connection, $em) {

    $result = "ERROR_UNKNOWN#Une erreur est survenue.";

    $query = $connection->prepare("SELECT * FROM kioui_tickets WHERE id = ?");
    $query->bind_param("i", $id);
    $query->execute();
    $result = $query->get_result();
    $query->close();
    $ticket = $result->fetch_assoc();

    //Autorisé à repondre ? => Session valide ET emetteur du ticket OU admin
    if (isValidSession($connection) && $ticket['user'] != "" && ($ticket['user'] == $_SESSION['Data']['id'] || $_SESSION['Data']['access_level'] == "ADMINISTRATOR")) {

        if ($ticket['status'] == "OPEN" || $ticket['status'] == "RESPONDED") {

            if (isset($message) && strlen($message) > 3) {

                $oldConversationArray = json_decode($ticket['conversation'], true);

                $role = ($_SESSION['Data']['access_level'] == "ADMINISTRATOR") ? "SUPPORT" : "USER";

                $messageArray = array(
                    "senderRole" => $role,
                    "senderName" => $_SESSION['Data']['username'],
                    "date" => time(),
                    "message" => $message
                );
                array_push($oldConversationArray, $messageArray);

                $conversation = json_encode($oldConversationArray);

                $newStatus = ($_SESSION['Data']['access_level'] == "ADMINISTRATOR") ? "RESPONDED" : "OPEN";

                $query = $connection->prepare("UPDATE kioui_tickets SET conversation = ? , status = ? WHERE id = ?");
                $query->bind_param("ssi", $conversation, $newStatus, $id);
                $query->execute();
                $query->close();

                //Assignation
                if ($_SESSION['Data']['access_level'] == "ADMINISTRATOR") {
                    $query = $connection->prepare("UPDATE kioui_tickets SET assigned = ? WHERE id = ?");
                    $query->bind_param("ii", $_SESSION['Data']['id'], $id);
                    $query->execute();
                    $query->close();
                }

                $result = "SUCCESS#Réponse envoyée.#./";

            } else {
                $result = "ERROR_MISSING_FIELDS#Veuillez remplir tous les champs.";
            }

        } else {
            $result = "ERROR_TICKET_CLOSED#Cette demande est fermée. Vous ne pouvez plus y repondre.";
        }

    } else {
        $result = "ERROR_INVALID_SESSION#Votre session est invalide. Déconnectez vous puis reconnectez vous. Si le problème persiste contactez le support.";
    }

    return $result;
}


/**
 * Fonction qui ferme un ticket support
 * 
 * @param integer             $id                   - Id du ticket a fermer
 * @param mysqlconnection     $connection           - Connexion BDD effectuée dans le fichier config-db.php
 * @param array               $em                   -   Identifiants email dans le fichier config-email.php
 * 
 * @return void
 */
function closeTicket($id, $connection, $em) {

    $query = $connection->prepare("SELECT * FROM kioui_tickets WHERE id = ?");
    $query->bind_param("i", $id);
    $query->execute();
    $result = $query->get_result();
    $query->close();
    $ticket = $result->fetch_assoc();

    if (isValidSession($connection) && $ticket['user'] != "" && ($ticket['user'] == $_SESSION['Data']['id'] || $_SESSION['Data']['access_level'] == "ADMINISTRATOR")) {

        if ($ticket['status'] == "OPEN" || $ticket['status'] == "RESPONDED") {

            $newStatus = "";
            if ($_SESSION['Data']['access_level'] == 'ADMINISTRATOR') {
                $newStatus = "CLOSED_BY_SUPPORT";
            } else {
                $newStatus = "CLOSED_BY_USER";
            }

            $messageArray = array(
                "senderRole" => "USER",
                "senderName" => "",
                "date" => time(),
                "message" => $_SESSION['Data']['username'] . " a fermé la demande de support."
            );

            $oldConversationArray = json_decode($ticket['conversation'], true);

            array_push($oldConversationArray, $messageArray);

            $conversation = json_encode($oldConversationArray);

            $query = $connection->prepare("UPDATE kioui_tickets SET status = ? , conversation = ? WHERE id = ?");
            $query->bind_param("ssi", $newStatus, $conversation, $id);
            $query->execute();
            $query->close();

        }

    }

    header("Location: /espace-utilisateur/assistance/" . $id . "/");

}


/**
 * Fonction qui change la priorité du ticket
 * 
 * @param integer             $id                   - Id du ticket a fermer
 * @param string              $priority             - Nouvelle priorité
 * @param mysqlconnection     $connection           - Connexion BDD effectuée dans le fichier config-db.php
 * 
 * @return void
 */
function priorTicket($id, $priority, $connection) {

    $query = $connection->prepare("SELECT * FROM kioui_tickets WHERE id = ?");
    $query->bind_param("i", $id);
    $query->execute();
    $result = $query->get_result();
    $query->close();
    $ticket = $result->fetch_assoc();

    if (isValidSession($connection) && $ticket['user'] != "" && $_SESSION['Data']['access_level'] == "ADMINISTRATOR") {

        if ($priority == "LOW" || $priority == "MEDIUM" || $priority == "HIGH" || $priority == "HIGHEST") {

            $query = $connection->prepare("UPDATE kioui_tickets SET priority = ? WHERE id = ?");
            $query->bind_param("si", $priority, $id);
            $query->execute();
            $query->close();

        }

    }

    header("Location: /espace-utilisateur/assistance/" . $id . "/");

}


/**
 * Fonction qui envoie le mail pour confirmer le changement d'email
 * @param string              $newEmail             - Email de remplacement
 * @param string              $password             - Mot de passe de l'utilisateur
 * @param mysqlconnection     $connection           - Connexion BDD effectuée dans le fichier config-db.php
 * @param array               $em                   - Identifiants email dans le fichier config-email.php
 * 
 */
function changeEmailConfirmation($newEmail, $password, $connection, $em) {
    $result = "ERROR_UNKNOWN#Une erreur est survenue.";
    
    if (isValidSession($connection)) {

        if (isset($newEmail, $password) && filter_var($newEmail, FILTER_VALIDATE_EMAIL) && $password != "") {

            //Verification email non utilisée
            $query = $connection->prepare("SELECT * FROM kioui_accounts WHERE email = ? OR email_toconfirm = ?");
            $query->bind_param("ss", $newEmail, $newEmail);
            $query->execute();
            $result = $query->get_result();
            $query->close();
            $verifData = $result->fetch_assoc();

            if ($verifData['id'] == "") {

                //Recuperation des données
                $query = $connection->prepare("SELECT * FROM kioui_accounts WHERE id = ?");
                $query->bind_param("i", $_SESSION['Data']['id']);
                $query->execute();
                $result = $query->get_result();
                $query->close();
                $userData = $result->fetch_assoc();

                //Vérification MDP
                if (password_verify(hash('sha512', hash('sha512', $password . $_SESSION['Data']['salt'])), $_SESSION['Data']['password'])) {

                    $emailToken = randomString(64);
                    $link = "https://ki-oui.ythepaut.com/verif-email/" . $emailToken ;

                    $query = $connection->prepare("UPDATE kioui_accounts SET email_toconfirm = ? , email_token = ? WHERE id = ?");
                    $query->bind_param("ssi", $newEmail, $emailToken, $_SESSION['Data']['id']);
                    $query->execute();
                    $query->close();

                    sendMail($em, $newEmail, "Changement de votre adresse e-mail", "CHANGEMENT DE VOTRE ADRESSE E-MAIL", "Bonjour " . $userData['username'] . "Afin de completer votre demande de changement d'adresse e-mail, veuillez cliquer sur le lien ci-dessous.<br /><br />Si vous n'êtes pas à l'origine de cette demande, changez votre mot de passe et contactez le support.", $link, "Confirmer ma nouvelle adresse");
                    $result = "SUCCESS#Un e-mail de vérification a été envoyé.#/espace-utilisateur/compte";

                } else {
                    $result = "ERROR_INVALID_CREDENTIALS#Mot de passe invalide.";
                }

            } else {
                $result = "ERROR_USED_EMAIL#Cette adresse e-mail est déjà utilisée.";
            }

        } else {
            $result = "ERROR_MISSING_FIELDS#Veuillez remplir tous les champs.";
        }

    } else {
        $result = "ERROR_INVALID_SESSION#Votre session est invalide. Déconnectez vous puis reconnectez vous. Si le problème persiste contactez le support.";
    }

    return $result;
}


/**
 * Verification d'email après enregistrement ou apres changement email
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

    } elseif ($userData["id"] != "" && $userData["email_toconfirm"] != "") {

        $none = "";
        $query = $connection->prepare("UPDATE kioui_accounts SET email = ? , email_toconfirm = ? , email_token = ? WHERE id = ?");
        $query->bind_param("sssi", $userData["email_toconfirm"], $none, $none, $userData["id"]);
        $query->execute();
        $query->close();

    }

    header("Location: /espace-utilisateur");

}

?>
