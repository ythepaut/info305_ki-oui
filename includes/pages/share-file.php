<div class="accueil container-fluid">
    <div class="row justify-content-center">
        <div class="col-5 align-self-center box" style="text-align:center;">
            <span>Fichier à télécharger :</span><br />
            <span style="text-align: center;" id="originalName-directdownload"></span>
            <br />

            <?php

            if (isset($_SESSION["error"])) {
                if ($_SESSION["error"] == "ok") {
                    echo("<h2>Ok</h2>");
                }
                else {
                    echo("<h2>Erreur : ".$_SESSION["error"]."</h2>");
                }
            }

            $_SESSION["error"] = null;

            ?>

            <form action="<?php echo(getSrc('./includes/classes/actions.php')); ?>" method="POST"  id="directDownloadForm" enctype="multipart/form-data">
                <input type="hidden" name="action" value="download-file" />

                <?php
                    if (isset($_GET["filename"])) {
                        $fileName = $_GET["filename"];
                    }
                    else {
                        $fileName = "";
                    }

                    if (isset($_GET["filekey"])) {
                        $fileKey = $_GET["filekey"];
                    }
                    else {
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
