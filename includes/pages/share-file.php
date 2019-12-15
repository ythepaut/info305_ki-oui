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
?>

<div class="accueil container-fluid">
    <div class="row justify-content-center" style="min-height: 75vh;">

        <div class="dlpage col-lg-4 row">

            <div class="col-lg-3">
                <h1><i class="fas fa-download"></i></h1>
            </div>

            <div class="col-lg-auto">
            </div>

            <div class="col-lg-8">
                <span style="font-size: 1.5rem;" id="originalName-directdownload">
                    <?php
                        list($fileRealName, $fileSize) = getFileInfos($fileName, $fileKey, $connection);

                        if ($fileRealName === null) {
                            echo "<span style='color: red'>Le fichier n'existe pas</span>";
                        }
                        else {
                            echo $fileRealName;
                        }
                     ?>
                </span>&nbsp;&nbsp;&nbsp;

                <?php
                    if ($fileRealName !== null) {
                        $s = "";

                        $s .= "<span class='badge badge-secondary' title='Taille'>" . convertUnits($fileSize) . "</span>";
                        $s .= "<br />";
                        $s .= "<form action=" . getSrc('./includes/classes/actions.php') . " method='POST'  id='directDownloadForm' enctype='multipart/form-data'>";
                        $s .= '<input type="hidden" name="action" value="download-file" />';
                        $s .= '<input type="hidden" name="filename" value="' . $fileName . '" id="path-directdownload" />';
                        $s .= '<input type="hidden" name="filekey"  value="' . $fileKey  . '" id="key-directdownload" />';
                        $s .= '<input type="submit" value="Télécharger" />';
                        $s .= '</form>';

                        echo($s);
                    }
                ?>

            </div>

        </div>

    </div>
</div>
