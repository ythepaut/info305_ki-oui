<div class="container-fluid">
    <div class="row">
        <?php include("./includes/pages/espace-utilisateur/nav-utilisateur.php"); ?>

        <section class="col-lg panel-background">
        <div class="row">

            <?php
            if (!isset($_GET['ticket']) && !isset($_GET['doc']) && $_SESSION['Data']['access_level'] != "ADMINISTRATOR") {

                include("./includes/pages/espace-utilisateur/assistance-utilisateur/liste.php");
            
            } elseif (!isset($_GET['ticket']) && !isset($_GET['doc']) && $_SESSION['Data']['access_level'] == "ADMINISTRATOR") {
                
                include("./includes/pages/espace-utilisateur/assistance-utilisateur/liste-admin.php");

            } elseif (isset($_GET['ticket']))  {
                
                include("./includes/pages/espace-utilisateur/assistance-utilisateur/conversation.php");
                
            } elseif (isset($_GET['doc']))  {

                include("./includes/pages/espace-utilisateur/assistance-utilisateur/documentation/documentation.php");

            }
            
            ?>

        </div>
        </section>

    </div>

</div>

<?php
include("./includes/pages/modals/create-ticket.php");
?>