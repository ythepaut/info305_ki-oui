<div class="accueil container-fluid">
    <div class="row justify-content-center">
        <div class="col-5 align-self-center box" style="text-align:center;">
            <span>Fichier à télécharger :</span><br />
            <span style="text-align: center;" id="originalName-directdownload"></span>;
            <br />

            <form action="<?php echo(getSrc('./includes/classes/actions.php')); ?>" method="POST" class="ajax" id="directDownloadForm" enctype="multipart/form-data">
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

                    echo '<input type="hidden" name="filename" value="' . $fileName . '" id="path-directdownload" />';
                    echo '<input type="hidden" name="filekey" value="' . $fileKey . '" id="key-directdownload" />';
                ?>

                <input type="submit" value="Download" />

                <div class="inner col-lg-4">
                    <h2> Télécharger </h2>
                    <a class="input-group-text label-icon" href='#' onclick='document.getElementById("directDownloadForm").submit()'><i class='fas fa-download edit'></i></a>
                </div>
            </form>
        </div>
    </div>
</div>
