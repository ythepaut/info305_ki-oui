<?php 
//Tableau temporaire en attendant l'exécution de la tâche correspondante
$query = mysqli_query($connection, "SELECT * FROM kioui_accounts");
$res = "<table class = 'table'>";
$res .= "<tr>";

//$res .= "<th style='width:1%;'><i class='far fa-user'></i></th>";
$res .= "<th style = 'width:auto'><i class='far fa-user edit'></i>Nom de l'utilisateur</th>";
$res .= "<th class='d-none d-lg-table-cell'>Niveau d'accès</th>";
$res .= "<th class='d-none d-lg-table-cell'>Changement du niveau d'accès</th>";

$res .= "</tr>";
while ($user = mysqli_fetch_assoc($query)) {
    $res .= "<tr>";
    //affichage du nom de l'utilisateur
    $res .= "<td class='d-none d-lg-table-cell'>";
    $res .= htmlspecialchars($user["username"]);
    $res .= "</td>";
    //affichage du niveau d'accès
    $res .= "<td class='d-none d-lg-table-cell'>";
    $res .= htmlspecialchars($user["access_level"]);
    $res .= "</td>";
    //changement du niveau d'accès
    $res .= "<td class='d-none d-lg-table-cell'>";
    $res .= "<a href='#' data-toggle='modal' data-target='#modalChangeAccessLevel' onclick='editModalAccessLevel(".$user['id'].")'><i class='fas fa-exchange-alt edit'></i></a>";
    $res .= "</td>";
    $res .= "</tr>";
}

$res .="</table>";
echo($res);
include("./includes/pages/modals/change-access-level.php");
?>
