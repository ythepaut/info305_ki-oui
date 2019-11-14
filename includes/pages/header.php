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

    <!--Chart.js(pour les graphiques)-->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.min.js"></script>
</head>
<body>



	<nav class="navbar sticky-top navbar-dark">
        <div class="col-lg-4">
            <a href="/" class="title navbar-brand"><i class="fas fa-kiwi-bird"></i> &nbsp; KI-OUI.FR</a>
        </div>
        <div class="col-lg-6">
            <form class="form-inline">
                <?php
                if (isset($_SESSION['LoggedIn']) && $_SESSION['LoggedIn'] && $_SESSION['Data']['Username']) {
                    echo('<a href="/espace-utilisateur/accueil"><span><i class="fas fa-tachometer-alt"></i> &nbsp; Espace utilisateur('.$_SESSION['Data']['Username'].')</span></a>');
                }else{
                    echo('<a href="#" data-toggle="modal" data-target="#modalLogin"><span><i class="fas fa-tachometer-alt"></i> &nbsp; Connexion</span></a>');
                }
                ?>
                <a href="#"><span><i class="fas fa-globe-americas"></i> &nbsp; EN</span></a>
            </form>
        </div>
	</nav>

    <?php
    include("./includes/pages/modals/login.php");
    include("./includes/pages/modals/register.php");
    include("./includes/pages/modals/upload-file-error.php");
    ?>

	<main>
