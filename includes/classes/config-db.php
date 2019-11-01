<?php

require_once("/home/ythepautfc/server/db-config-kioui.php");

$connection = mysqli_connect($db['ip'], $db['login'], $db['password'], $db['login']); 
mysqli_set_charset($connection, "utf8");

?>
