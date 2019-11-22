<div class="container-fluid">
    <div class="row">
        <?php include("./includes/pages/espace-utilisateur/nav-utilisateur.php"); ?>

        <section class="col-lg-10 panel-background">
        <div class="row">

            <?php
            if (!isset($_GET['ticket']) && $_SESSION['Data']['access_level'] != "ADMINISTRATOR") {

                include("./includes/pages/espace-utilisateur/assistance-utilisateur/liste.php");
            
            } elseif (!isset($_GET['ticket']) && $_SESSION['Data']['access_level'] == "ADMINISTRATOR") {
                
                include("./includes/pages/espace-utilisateur/assistance-utilisateur/liste-admin.php");

            } else {
                
                include("./includes/pages/espace-utilisateur/assistance-utilisateur/conversation.php");
                
            }
            
            ?>

        </div>
        </section>

    </div>

</div>

<?php
include("./includes/pages/modals/create-ticket.php");
?>