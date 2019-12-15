<div class="accueil container-fluid">
    <div class="row justify-content-center" style="min-height: 75vh;">



        <div class="dlpage col-lg-4 row">
            
            <div class="col-lg-3">
                <h1><i class="fas fa-download"></i></h1>
            </div>

            <div class="col-lg-auto">
            </div>

            <div class="col-lg-8">
                <span style="font-size: 1.5rem;" id="originalName-directdownload">{NOM DU FICHIER}</span>&nbsp;&nbsp;&nbsp;<span class='badge badge-secondary' title='Taille'>0 Mo</span>

                <br />

                <form action="<?php echo(getSrc('./includes/classes/actions.php')); ?>" method="POST"  id="directDownloadForm" enctype="multipart/form-data">
                    <input type="hidden" name="action" value="download-file" />

                    <?php
                        if (isset($_GET["filename"])) {
                            $fileName = $_GET["filename"];
                        } else {
                            $fileName = "";
                        }

                        if (isset($_GET["filekey"])) {
                            $fileKey = $_GET["filekey"];
                        } else {
                            $fileKey = "";
                        }

                        echo('<input type="hidden" name="filename" value="' . $fileName . '" id="path-directdownload" />');
                        echo('<input type="hidden" name="filekey" value="' . $fileKey . '" id="key-directdownload" />');
                    ?>

                    <input type="submit" value="Télécharger" />

                </form>
                
            </div>

        </div>

    </div>
</div>
