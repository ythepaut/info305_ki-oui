<?php
#Header
include("./includes/pages/header.php");

$page = (isset($_GET['page'])) ? $_GET['page'] : "";

switch ($page) {
    #Pages "vitrine"
    case "":
    case "index":
    case "accueil":
        include("./includes/pages/accueil.php");
        break;

    case "espace-utilisateur":
        if (isset($_SESSION['LoggedIn']) && $_SESSION['LoggedIn']) {
            refreshSession($connection);
            $sousPage = (isset($_GET['sp']) ? $_GET['sp'] : "accueil");
            switch ($sousPage) {
                case "compte":
                    include("./includes/pages/espace-utilisateur/compte-utilisateur.php");
                    break;
                case "assistance":
                    include("./includes/pages/espace-utilisateur/assistance-utilisateur.php");
                    break;
                case "accueil":
                default:
                    include("./includes/pages/espace-utilisateur/accueil-utilisateur.php");
                    break;
            }
        } else {
            include("./includes/pages/401.php");
            $openLoginModal = true;
        }
        break;

    case "ajout":
        include("./includes/pages/ajout.php");

        if (!(isset($_SESSION['LoggedIn']) && $_SESSION['LoggedIn'])) {
            $openLoginModal = true;
        }
        break;

    case "ajout-ok":
        include("./includes/pages/ajout-ok.php");
        break;

    case "ajout-nok":
        include("./includes/pages/ajout-nok.php");
        break;

    case "cgu":
    case "mentions-legales":
        include("./includes/pages/cgu.php");
        break;

    case "no-script":
        include("./includes/pages/no-script.php");
        break;

    case "403":
        include("./includes/pages/403.php");
        break;

    case "401":
        include("./includes/pages/401.php");
        break;

    case "404":
    default:
        include("./includes/pages/404.php");
        break;
}


#Footer
include("./includes/pages/footer.php");

if (isset($openLoginModal) && $openLoginModal) {
    echo("<script>$('#modalLogin').modal('show')</script>");
}
?>
