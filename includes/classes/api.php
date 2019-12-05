<?php
//Fichier qui gère l'ensemble des requetes API publique

session_start();

include_once(getcwd() . "/config-db.php");
include_once(getcwd() . "/config-email.php");
include_once(getcwd() . "/config-recaptcha.php");
include_once(getcwd() . "/config-server.php");
include_once(getcwd() . "/utils.php");
include_once(getcwd() . "/totp.php");
include_once(getcwd() . "/u2f.php");
require_once(getcwd() . '/PHPMailer/PHPMailerAutoload.php');

/**
 * Liste des erreurs :
 * 
 * ERROR_UNSAFE_CONNECTION          -       Connexion non sécurisée
 * ERROR_INVALID_ARGUMENT           -       Argument de la requete invalide
 * ERROR_UNKNOWN                    -       Erreur inconnue
 * ERROR_INVALID_CREDENTIALS        -       Identifiants incorrects
 * ERROR_ACCOUNT_INACCESSIBLE       -       Compte suspendu / Non validé / En attente de suppression / Niveau d'acces invalide
 */

if (!isset($_SERVER['HTTPS'])) {
    die(json_encode(array("status" => "error", "error" => "ERROR_UNSAFE_CONNECTION", "verbose" => "HTTPS is required to execute API requests."), JSON_PRETTY_PRINT));
}

$action = (isset($_POST['action'])) ? $_POST['action'] : "";
$action = ($action == "" && isset($_GET['action']) && $_GET['action'] != "") ? $_GET['action'] : $action;

$query = (isset($_POST['action'])) ? $_POST : $_GET;

switch ($action) {

    case "auth":
        die(json_encode(auth($query, $connection), JSON_PRETTY_PRINT));
        break;

    default:
        die(json_encode(array("status" => "error", "error" => "ERROR_INVALID_ARGUMENT", "verbose" => "Missing or invalid argument `action`."), JSON_PRETTY_PRINT));
        break;

}


/**
 * Fonction qui authentifie un utilisateur et retourne un jeton de connexion
 * (Requete API - Extension)
 * 
 * @param string            $query              -   Requete API
 * @param mysqlconnection   $connection         -   Connexion BDD effectuée dans le fichier config-db.php          
 * 
 * @return array
 */
function auth($query, $connection) {

    $result = array("status" => "error", "error" => "ERROR_UNKNOWN", "verbose" => "An unknown error occured during the process. This is our fault.");

    if (isset($query['email'], $query['passwd'], $query['duration']) && filter_var($query['email'], FILTER_VALIDATE_EMAIL) && $query['duration'] > 0 && $query['duration'] < 2592000) {

        //Recuperation des données
        $dbquery = $connection->prepare("SELECT * FROM kioui_accounts WHERE email = ?");
        $dbquery->bind_param("s", $query['email']);
        $dbquery->execute();
        $dbresult = $dbquery->get_result();
        $dbquery->close();
        $userData = $dbresult->fetch_assoc();

        if (isset($userData['id']) && $userData['id'] != null && password_verify(hash('sha512', hash('sha512', $query['passwd'] . $userData['salt'])), $userData['password'])) {

            //Verification du compte
            if ($userData['access_level'] != "" && $userData['status'] == "ALIVE") {

                $token = randomString(64);
                $expire = time() + $query['duration'];

                $dbquery = $connection->prepare("UPDATE kioui_accounts SET auth_token = ? , auth_token_expire = ? WHERE id = ?");
                $dbquery->bind_param("sii", $token, $expire, $userData['id']);
                $dbquery->execute();
                $dbquery->close();

                $result = array("status" => "success", "message" => "Your token has been generated.", "token" => $token);

            } else {
                $result = array("status" => "error", "error" => "ERROR_ACCOUNT_INACCESSIBLE", "verbose" => "This account is not validated or it is suspended or is in cancellation procedure.");
            }

        } else {
            $result = array("status" => "error", "error" => "ERROR_INVALID_CREDENTIALS", "verbose" => "Arguments `email` and `passwd` do not match with any account.");
        }

    } else {
        if (!isset($query['email']) || !filter_var($query['email'], FILTER_VALIDATE_EMAIL)) {
            $result = array("status" => "error", "error" => "ERROR_INVALID_ARGUMENT", "verbose" => "Missing or invalid argument `email`.");
        } elseif (!isset($query['passwd'])) {
            $result = array("status" => "error", "error" => "ERROR_INVALID_ARGUMENT", "verbose" => "Missing or invalid argument `passwd`.");
        } elseif (!isset($query['duration']) || $query['duration'] > 0 || $query['duration'] < 2592000) {
            $result = array("status" => "error", "error" => "ERROR_INVALID_ARGUMENT", "verbose" => "Missing or invalid argument `duration`.");
        }
    }

    return $result;

}








?>