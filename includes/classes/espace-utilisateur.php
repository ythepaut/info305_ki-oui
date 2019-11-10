<?php
    switch($page){
        case "espace-utilisateur":
            include("./includes/pages/espace-utilisateur/acceuil_utilisateur.php");
            break;
        case "compte-utilisateur":
            include("./includes/pages/espace-utilisateur/compte_utilisateur.php");
            break;
        case "aide-utilisateur":
        include("./includes/pages/espace-utilisateur/assistance_utilisateur.php");
            break;
    }
?>
