<div class="col panel-outline">
    
    
    <?php

    switch ($_GET['doc']) {
        case "modele-chiffrement":
            include("./includes/pages/espace-utilisateur/assistance-utilisateur/documentation/modele-chiffrement.php");
            break;
        case "api":
            include("./includes/pages/espace-utilisateur/assistance-utilisateur/documentation/api.php");
            break;
        case "extension":
            include("./includes/pages/espace-utilisateur/assistance-utilisateur/documentation/extension.php");
            break;
        default:
            include("./includes/pages/404.php");
            break;
    }

    ?>

</div>


<?php include("./includes/pages/espace-utilisateur/assistance-utilisateur/incidents-ressources.php") ?>
