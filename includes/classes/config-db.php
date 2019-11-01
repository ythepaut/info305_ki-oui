<?php

<<<<<<< HEAD
$db = array();

$db['ipdatabase'] = 'mysql:host=;dbname=';
$db['ip'] = '';
$db['login'] = '';
$db['password'] = '';
=======
require_once("/home/ythepautfc/server/db-config-kioui.php");
>>>>>>> Structure global + Accueil + CSS

$connection = mysqli_connect($db['ip'], $db['login'], $db['password'], $db['login']); 
mysqli_set_charset($connection, "utf8");

<<<<<<< HEAD
?>
=======
?>
>>>>>>> Structure global + Accueil + CSS
