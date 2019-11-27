<!DOCTYPE html>

<html lang="fr">
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1.0" />
        <meta http-equiv="X-UA-Compatible" content="ie=edge" />
    	<title>Ki-Oui</title>
        <link rel="icon" type="image/png" href="<?php echo(getSrc('./ressources/img/favicon.png')); ?>" />

        <!--Bootstrap-->
    	<link rel="stylesheet" type="text/css" href="<?php echo(getSrc('./css/bootstrap.css')); ?>" />
        <link rel="stylesheet" type="text/css" href="<?php echo(getSrc('./css/bootstrap-grid.css')); ?>" />

        <link rel="stylesheet" type="text/css" href="<?php echo(getSrc('./css/style.css')); ?>" />

        <!--ChartJs -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js"></script>
    </head>

    <body>
        <div class="page-container">
        <div class="page-content">

    	<nav class="navbar sticky-top navbar-dark">
            <div class="col-lg-4">
                <a href="/" class="title navbar-brand"><i class="fas fa-kiwi-bird"></i> &nbsp; KI-OUI.FR</a>
            </div>
            <div class="col-lg-6">
                <form class="form-inline">
                    <?php
                    if (isset($_SESSION['LoggedIn']) && $_SESSION['LoggedIn']) {
                        echo('<a href="/espace-utilisateur/accueil"><span><i class="fas fa-tachometer-alt"></i> &nbsp; Espace utilisateur (' . htmlspecialchars($_SESSION['Data']['username']) . ')</span></a>');
                        echo('<a href="/logout"><span><i class="fas fa-sign-out-alt"></i> &nbsp; Deconnexion</span></a>');
                    }else{
                        echo('<a href="#" data-toggle="modal" data-target="#modalLogin"><span><i class="fas fa-tachometer-alt"></i> &nbsp; Espace utilisateur</span></a>');
                    }
                    ?>
                    <a href="#"><span><i class="fas fa-globe-americas"></i> &nbsp; EN</span></a>
                </form>
            </div>
    	</nav>

        <noscript>
            <?php
                if (!isset($_GET["page"]) || $_GET["page"] != "no-script") {
                    echo("<meta http-equiv='refresh' content='0; url=/no-script' />");
                }
            ?>
        </noscript>

        <?php
            if (!isset($_SESSION['LoggedIn'])) {
                include("./includes/pages/modals/login.php");
                include("./includes/pages/modals/register.php");
                include("./includes/pages/modals/forgot-password.php");
            }

            include("./includes/pages/modals/upload-file-error.php");
        ?>

    	<main>
