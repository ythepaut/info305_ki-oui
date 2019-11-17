<?php
session_start();

#Affichage des message de debug
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);


#Configurations
include_once("./includes/classes/config-db.php");
include("./includes/classes/config-recaptcha.php");
include_once("./includes/classes/config-email.php");
include_once("./includes/classes/totp.php");
$ga = new PHP_GoogleAuthenticator();



#Fonctions utiles
include_once("./includes/classes/utils.php");


#Affichage de la page web
include("./includes/classes/view.php");

/*
https://ki-oui.ythepaut.com/share-file/KdQH6CsQLN8undhb/797627c112aebba19fae260f49b4715d75aefdaf42c72fbea333aec80d0fdb393ff0118d0afe64c12d527193491b8d76c727154b3b4d272dd841089a53183149


RewriteRule ^share-file/([A-Za-z0-9-]+)/([A-Za-z0-9-]+)$ includes/pages/share-file.php?filename=$1&filekey=$2 [L]
 */

?>
