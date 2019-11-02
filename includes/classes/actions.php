<?php
//Fichier qui gère l'ensemble des formulaire POST

include("./config-db.php");

$action = (isset($_POST['action'])) ? $_POST['action'] : "";

switch ($action) {
    case "login":
        die(login($_POST['login_email'], $_POST['login_passwd'], $connection));
        break;

    case "upload":
        $res = upload($_FILES['files']);

        if ($res) {
            $loc = "/ajout-ok";
        }
        else {
            $loc = "/ajout-nok";
        }

        // header("location:$loc");
        break;

    default:
        throw new Exception("Action invalide : " . '$action' . " = '$action'");
        break;
}




/**
 * Connexion de l'utilisateur : Methode e-mail + mot de passe
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

                $result = "SUCCESS#Bienvenue " . $_SESSION['Data']['username'];

            } else {

                switch ($userData['status']) {
                    case "SUSPENDED":
                        $result = "ERROR_ACCOUNT_SUSPENDED#Connexion impossible : Ce compte est suspendu.";
                        break;
                    case "REGISTRATION":
                        $result = "ERROR_ACCOUNT_UNVERIFIED#Connexion impossible : Veuillez verifier votre e-mail.";
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
 * Upload des fichiers
 *
 * @param array             $files              -   Fichiers envoyés
 *
 * @return boolean                              -   Si l'opération s'est bien passée ou non
 */
function upload($files) {
    var_dump($files["name"][0]);

    echo "Débug en cours <br />";

    for ($i=0; $i<count($files["name"]); $i++) {
        echo "Nom : " . $files["name"][$i] . " <br />";
        echo "Type : " . $files["type"][$i] . " <br />";
        echo "Tmp name : " . $files["tmp_name"][$i] . " <br />";
        echo "Error : " . $files["error"][$i] . "<br />";
        echo "Size : " . $files["size"][$i] . "<br />";
        echo "<br />";
    }

    return true;
}

?>
