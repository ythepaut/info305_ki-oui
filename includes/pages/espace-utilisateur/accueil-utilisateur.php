<div class="container-fluid">
    <div class="row">
        <?php include("./includes/pages/espace-utilisateur/nav-utilisateur.php"); ?>

        <section class="col-lg panel-background">
            <div class="row">

                <div class="col-lg-4 panel-outline">

                    <h4 class="panel-title">Espace utilisé</h4>

                    <div class="chart-container">
                        <canvas id="chart-js-1" class="chartjs quota"></canvas>
                    </div>

                    <script>
                        /*
                        * Fonction pour mettre du texte au milieu d'un donut
                        */
                        Chart.pluginService.register({
                            beforeDraw: function (chart) {
                            if (chart.config.options.elements.center) {
                                //Get ctx from string
                                var ctx = chart.chart.ctx;

                                //Get options from the center object in options
                                var centerConfig = chart.config.options.elements.center;
                                var fontStyle = centerConfig.fontStyle || 'Arial';
                                var txt = centerConfig.text;
                                var color = centerConfig.color || '#000';
                                var sidePadding = centerConfig.sidePadding || 20;
                                var sidePaddingCalculated = (sidePadding/100) * (chart.innerRadius * 2)
                                //Start with a base font of 30px
                                ctx.font = "30px " + fontStyle;

                                //Get the width of the string and also the width of the element minus 10 to give it 5px side padding
                                var stringWidth = ctx.measureText(txt).width;
                                var elementWidth = (chart.innerRadius * 2) - sidePaddingCalculated;

                                // Find out how much the font can grow in width.
                                var widthRatio = elementWidth / stringWidth;
                                var newFontSize = Math.floor(30 * widthRatio);
                                var elementHeight = (chart.innerRadius * 2);

                                // Pick a new font size so it will not be larger than the height of label.
                                var fontSizeToUse = Math.min(newFontSize, elementHeight);

                                //Set font settings to draw it correctly.
                                ctx.textAlign = 'center';
                                ctx.textBaseline = 'middle';
                                var centerX = ((chart.chartArea.left + chart.chartArea.right) / 2);
                                var centerY = ((chart.chartArea.top + chart.chartArea.bottom) / 2) + 30;
                                ctx.font = fontSizeToUse+"px " + fontStyle;
                                ctx.fillStyle = color;

                                //Draw text in center
                                ctx.fillText(txt, centerX, centerY);
                            }
                            }
                        });

                        <?php

                        $pourcentage = round((getSize($_SESSION['Data']['id'], $connection) / $_SESSION['Data']['quota']) * 100, 2);
                        $pourcentage = ($pourcentage == 0) ? "0.00" : $pourcentage;
                        $occupe = round(getSize($_SESSION['Data']['id'], $connection)/(10**6), 2);
                        $restant = round($_SESSION['Data']['quota']/(10**6) - getSize($_SESSION['Data']['id'], $connection)/(10**6), 2);
                        $restant = ($occupe < $_SESSION['Data']['quota']/(10**6)) ? $restant : 0;

                        $couleur = ($restant > 0) ? "#54a0ff" : "#ee5253";

                        ?>

                        var options = {
                            maintainAspectRatio: false,
                            rotation: 1 * Math.PI,
                            circumference: 1 * Math.PI,
                            cutoutPercentage: 70,
                            legend: {
                                display: true,
                                position: 'right',
                                fullWidth: false,
                                onClick: null
                            },
                            elements: {
                                center: {
                                    text: "<?php echo($pourcentage); ?>%",
                                    color: '<?php echo($couleur); ?>',
                                    fontStyle: 'Helvetica',
                                    sidePadding: 42
                                }
                            }
                        };
                        //variable contenant l'espace utilisé par l'utilisateur
                        var data = {
                            datasets: [{
                                data:[<?php echo($occupe); ?>, <?php echo($restant); ?>],
                                backgroundColor:["<?php echo($couleur); ?>","rgb(200, 214, 229)"],
                                weight: 15,
                            }],
                            labels: [
                                'Espace Utilisé (Mo)',
                                'Espace Restant (Mo)'
                            ]
                        };
                        new Chart(document.getElementById("chart-js-1"),{
                            type: 'doughnut',
                            data: data,
                            options: options,
                        });
                    </script>
                </div>

                <div class="col-lg inner panel-outline">
                    <form action="<?php echo(getSrc('./includes/classes/actions.php')); ?>" method="post" enctype="multipart/form-data" id="uploadForm">
                        <h4 class="panel-title"> Ajouter des fichiers </h4>

                        <label for="inputFile" id="inputLabel"><i class="fas fa-file-import"></i> Ajouter des fichiers </label>
                        <div id="allInputs"></div>

                        <table class="table" id="files_tab">
                            <thead class="thead-light">
                                <th scope="col">Nom</th>
                                <th scope="col">Taille</th>
                                <th scope="col">Progression</th>
                            </thead>

                            <input type="text" name="action" value="upload-file" hidden />

                            <tr id="first_line_tab">
                                <td scope="row" colspan="2"><i>Aucun fichier sélectionné</i></td>
                            </tr>
                        </table>


                        <script type="text/javascript" src="<?php echo(getSrc('./js/upload.js')); ?>"></script>
                        <script>init();</script>
                    </form>
                </div>

            </div>
            <div class="row">
                <div class="col panel-outline">

                    <h4 class="panel-title">Mes fichiers &nbsp;<?php if (isset($_GET['sp']) && $_GET['sp'] == "grid") { ?> <a href="/espace-utilisateur/sort-by-date" style="position: absolute; right: 20px;"><i class="fas fa-table sort" title="Affichage tableau"></i></a> <?php } else { ?> <a href="/espace-utilisateur/grid" style="position: absolute; right: 20px;"><i class="fas fa-th sort" title="Affichage grille"></i></a> <?php } ?></h4>


                    <?php
                    //Tri
                    if(isset($_GET['sp'])) {
                        switch ($_GET['sp']) {
                            case "sort-by-name":
                                $_SESSION['table_files_sort'] = "original_name/ASC";
                                break;
                            case "sort-by-size":
                                $_SESSION['table_files_sort'] = "size/DESC";
                                break;
                            case "sort-by-dl":
                                $_SESSION['table_files_sort'] = "download_count/DESC";
                                break;
                            case "sort-by-date":
                            default:
                                $_SESSION['table_files_sort'] = "id/DESC";
                                break;
                        }
                    }
                    ?>

                    <?php if (!(isset($_GET['sp']) && $_GET['sp'] == "grid")) { ?>

                    <table class="table">
                        <thead class="thead">
                            <th style="width:auto;">Nom du fichier &nbsp;
                            <?php if (isset($_GET['sp']) && $_GET['sp'] == "sort-by-name-desc") { ?>
                            <a href="/espace-utilisateur/sort-by-name-asc"><i class="fas fa-sort sort <?php if ($_SESSION['table_files_sort'] == "original_name/ASC") {echo("active"); } ?>" title="Trier par nom"></i></a>
                            <?php } else { ?> 
                            <a href="/espace-utilisateur/sort-by-name-desc"><i class="fas fa-sort sort <?php if ($_SESSION['table_files_sort'] == "original_name/ASC") {echo("active"); } ?>" title="Trier par nom"></i></a> 
                            <?php } ?></th>
                            <th style="width:15%;" class="d-none d-lg-table-cell">Taille du fichier &nbsp;<a href="/espace-utilisateur/sort-by-size"><i class="fas fa-sort sort <?php if ($_SESSION['table_files_sort'] == "size/DESC") {echo("active"); } ?>" title="Trier par taille"></i></a></th>
                            <th style="width:15%;" class="d-none d-lg-table-cell">Date &nbsp;<a href="/espace-utilisateur/sort-by-date"><i class="fas fa-sort sort <?php if ($_SESSION['table_files_sort'] == "id/DESC") {echo("active"); } ?>" title="Trier par date"></i></a></th>
                            <th style="width:15%;" class="d-none d-lg-table-cell">Téléchargements &nbsp;<a href="/espace-utilisateur/sort-by-dl"><i class="fas fa-sort sort <?php if ($_SESSION['table_files_sort'] == "download_count/DESC") {echo("active"); } ?>" title="Trier nombre de téléchargements"></i></a></th>
                            <th style="width:15%;">Actions</th>
                        </thead>
                    <?php } ?>
                        <?php


                        $key = $_SESSION['UserPassword'];
                        $files = getFiles($_SESSION['Data']['id'], $connection, $_SESSION['table_files_sort']);

                        if (isset($_GET["sp"])) {
                            if ($_GET['sp'] == "sort-by-name-desc") {
                                foreach ($files as $file) {
                                    $file["original_name"] = decryptText($file["original_name"], $key, $file["salt"], null, false);
                                }
                                $filesArrayObject = new ArrayObject($files);
                                $filesArrayObject->asort();
                                $files = [];
                                foreach ($filesArrayObject as $fileArrayObject) {
                                    array_unshift($files, $fileArrayObject);
                                }
                            } else if ($_GET['sp'] == "sort-by-name-asc") {
                                foreach ($files as $file) {
                                    $file["original_name"] = decryptText($file["original_name"], $key, $file["salt"], null, false);
                                }
                                $filesArrayObject = new ArrayObject($files);
                                $filesArrayObject->asort();
                                $files = $filesArrayObject;
                            }
                        }

                        $table = "";
                        $grid = "<div class='row'>";
                        $count = 0;

                        foreach($files as $file){
                            $path = $file["path"];

                            $originalName = decryptText($file["original_name"], $key, $file["salt"], null, false);
                            $originalName = htmlspecialchars($originalName);
                            $originalName = str_replace("'", "&apos;", $originalName);
                            $originalName = str_replace("\"", '&quot;', $originalName);

                            $originalName = (strlen($originalName) > 70) ? substr($originalName, 0, 67) . "..." : $originalName;

                            //Colonne Nom
                            $table .=  "<tr><td><span title='" . htmlspecialchars(decryptText($file["original_name"], $key, $file["salt"], null, false)) . "'>" . $originalName . "</span></td>\n";
                            //Colonne Taille
                            $table .=  "<td class='d-none d-lg-table-cell'>" . convertUnits($file["size"]) . "</td>\n";
                            //Colonne Date
                            $table .=  "<td class='d-none d-lg-table-cell'>" . date("d/m/Y", $file["upload_date"]) . "&nbsp;&nbsp;&nbsp;" . date("H:i:s", $file["upload_date"]) . "</td>\n";
                            //Colonne Date
                            $table .=  "<td class='d-none d-lg-table-cell'>" . $file["download_count"] . "</td>\n";
                            //Colonne Action
                            $table .=  "<td>" . "<a href='#' data-toggle='modal' data-target='#modalShareLink' onclick='editModalShare(\"" . generateShareLink($_SESSION['UserPassword'], $file['id'], $connection) . "\")'><i class='fas fa-share-alt edit'></i></a>" . "&nbsp; &nbsp; &nbsp;" .
                                                "<a href='#' data-toggle='modal' data-target='#modalDirectDownload' onclick='editModalDirectDownload(".'"'."$path".'"'.", ".'"'."$key".'"'.", ".'"'.$originalName.'"'.")'><i class='fas fa-download edit'></i></a>" . "&nbsp; &nbsp; &nbsp;" .
                                                "<a href='#' data-toggle='modal' data-target='#modalDeleteFile' onclick='editModalDelete(" . $file['id'] . ")'><i class='fas fa-trash-alt delete'></i></a>" . "</td></tr>\n";

                            //Affichage grille
                            $count++;
                            if ($count == 12) {
                                $grid .= "</div>";
                                $grid .= "<div class='row'>";
                                $count = 1;
                            }

                            $extension = explode(".", decryptText($file["original_name"], $key, $file["salt"], null, false))[count(explode(".", decryptText($file["original_name"], $key, $file["salt"], null, false))) - 1];
                            $fileImg = "https://img.icons8.com/dusk/256/000000/file--v2.png";
                            switch ($extension) {
                                case "png":
                                case "jpg":
                                case "jpeg":
                                case "gif":
                                    $fileImg = "https://img.icons8.com/dusk/256/000000/picture.png";
                                    break;
                                case "pdf":
                                    $fileImg = "https://img.icons8.com/dusk/256/000000/pdf.png";
                                    break;
                                case "wav":
                                case "mp3":
                                case "flac":
                                    $fileImg = "https://img.icons8.com/dusk/256/000000/musical.png";
                                    break;
                                case "mp4":
                                case "wmv":
                                    $fileImg = "https://img.icons8.com/dusk/256/000000/video-file.png";
                                    break;
                                case "zip":
                                case "gz":
                                case "rar":
                                case "7zip":
                                    $fileImg = "https://img.icons8.com/dusk/256/000000/archive-folder.png";
                                    break;
                                case "txt":
                                    $fileImg = "https://img.icons8.com/dusk/256/000000/txt.png";
                                    break;

                            }


                            $originalName = (strlen($originalName) > 19) ? substr($originalName, 0, 16) . "..." : $originalName;
                            $grid .= "<div class='col-lg-1 item-grid'><a href='#' style='color: #000;' data-toggle='modal' data-target='#modalShareLink' onclick='editModalShare(\"" . generateShareLink($_SESSION['UserPassword'], $file['id'], $connection) . "\")'>" .
                                     "<h3 title='" . htmlspecialchars(decryptText($file["original_name"], $key, $file["salt"], null, false)) . "'>" . $originalName . "</h3>\n" .
                                     "<img src='" . $fileImg . "' alt='Logo' /><br />\n" .
                                     "</a></div>";



                        }

                        if (isset($_GET['sp']) && $_GET['sp'] == "grid") {
                            echo($grid . "</div>");
                        } else {
                            echo($table);
                        }

                        ?>

                    <?php if (!(isset($_GET['sp']) && $_GET['sp'] == "grid")) { ?>
                    </table>
                    <?php } ?>



                </div>
            </div>
        </section>
    </div>
</div>

<?php
include("./includes/pages/modals/direct-download.php");
include("./includes/pages/modals/share-link.php");
include("./includes/pages/modals/delete-file.php");
?>
