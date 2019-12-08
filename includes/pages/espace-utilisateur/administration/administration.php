<style>
::-webkit-scrollbar {
    width: 7px !important;
}
</style>

<div class="container-fluid">
    <div class="row">
        <?php include("./includes/pages/espace-utilisateur/nav-utilisateur.php"); ?>

        <section class="col-lg panel-background">
        <div class="row">

            <div class="col" style="padding: 0px; margin: 20px 15px;">
                <!--Tab nav-->
                <ul class="nav nav-tabs" role="tablist">
                    <li class="nav-item">
                        <a class="nav-link active link" style="border: 0px solid transparent;" id="stac-tab" data-toggle="tab" href="#stac" role="tab" aria-controls="stac" aria-selected="true" onclick="window.location.href='/espace-utilisateur/administration/#/statistiques';">Statistiques et actions globales</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link link" style="border: 0px solid transparent;" id="gesut-tab" data-toggle="tab" href="#gesut" role="tab" aria-controls="gesut" aria-selected="false" onclick="window.location.href='/espace-utilisateur/administration/#/utilisateurs';">Gestion des utilisateurs</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link link" style="border: 0px solid transparent;" id="gesfi-tab" data-toggle="tab" href="#gesfi" role="tab" aria-controls="gesfi" aria-selected="false" onclick="window.location.href='/espace-utilisateur/administration/#/fichiers';">Gestion des fichiers</a>
                    </li>
                </ul>


                <div class="col">

                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="stac" role="tabpanel" aria-labelledby="stac-tab">
                            <?php include("./includes/pages/espace-utilisateur/administration/statistiques.php"); ?>
                        </div>
                        <div class="tab-pane fade" id="gesut" role="tabpanel" aria-labelledby="gesut-tab">
                            <?php include("./includes/pages/espace-utilisateur/administration/utilisateurs.php"); ?>
                        </div>
                        <div class="tab-pane fade" id="gesfi" role="tabpanel" aria-labelledby="gesfi-tab">
                            <?php include("./includes/pages/espace-utilisateur/administration/fichiers.php"); ?>
                        </div>
                    </div>
                    


                </div>
            </div>

        </div>
        </section>

    </div>

</div>