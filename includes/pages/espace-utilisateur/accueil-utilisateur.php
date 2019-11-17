<div class="container-fluid">
    <div class="row">
        <?php include("./includes/pages/espace-utilisateur/nav-utilisateur.php"); ?>

        <section class="col-lg-10 panel-background">
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
                                    text: "<?php echo(substr((getSize($_SESSION['Data']['id'], $connection) / $_SESSION['Data']['quota']) * 100, 0, 4)); ?>%",
                                    color: '#54a0ff',
                                    fontStyle: 'Helvetica',
                                    sidePadding: 42
                                }
                            }
                        };
                        //variable contenant l'espace utilisé par l'utilisateur
                        var data = {
                            datasets: [{
                                data:[<?php echo(substr(getSize($_SESSION['Data']['id'],$connection)/(10**6),0,5)); ?>, <?php echo(substr(200 - getSize($_SESSION['Data']['id'],$connection)/(10**6),0,5)); ?>],
                                backgroundColor:["rgb(84, 160, 255)","rgb(200, 214, 229)"],
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
                    <h4 class="panel-title">Ajouter des fichiers</h4>

                    <a href="/upload-file" class="button"><i class="fas fa-file-import"></i> &nbsp; Ajouter un fichier</a>
                </div>

            </div>
            <div class="row">
                <div class="col panel-outline">

                    <div class="row">
                    <div class="col-lg-8">
                        <h4 class="panel-title">Mes fichiers</h4>
                    </div>
                    <div class="col-lg" style="padding: 15px 0px 12px 0px;">
                        <span>Tier par : </span>
                        <div class="btn-group" role="group" aria-label="Trie">
                            <button type="button" onclick="window.alert('J\'ai perdu.')">Nom</button>
                            <button type="button" onclick="window.alert('J\'ai perdu.')">Date d'ajout</button>
                            <button type="button" onclick="window.alert('J\'ai perdu.')">Nombre de téléchargements</button>
                        </div>
                    </div>
                    </div>

                    <table class="table">
                        <thead class="thead">
                            <th style="width=60%;">Nom du fichier</th>
                            <th style="width=20%;">Taille du fichier</th>
                            <th style="width=auto;">Actions</th>
                        </thead>
                        <?php
                        $files = getFiles($_SESSION['Data']['id'],$connection);
                        $res = "";
                        foreach($files as $file){
                            $res .= "<tr>\n";
                            $res .= "<td>";
                            $res .= $file["original_name"];
                            $res .= "</td>\n";
                            $res .= "<td>";
                            $res .= convertUnits($file["size"]);
                            $res .= "</td>\n";
                            $res .= "<td>";
                            $res .= "<a href='#' data-toggle='modal' data-target='#modalShareLink' onclick='editModalShare(\"" . generateShareLink($_SESSION['UserPassword'], $file['id'], $connection) . "\")'><i class='fas fa-share-alt edit'></i></a>";
                            $res .= "&nbsp; &nbsp; &nbsp;\n";
                            $res .= "<a href='#' data-toggle='modal' data-target='#modalDeleteFile' onclick='editModalDelete(" . $file['id'] . ")'><i class='fas fa-trash-alt edit'></i></a>";
                            $res .= "&nbsp; &nbsp; &nbsp;\n";

                            $path = $file["path"];
                            $key = $_SESSION['UserPassword'];
                            $originalName = htmlspecialchars($file["original_name"]);
                            $originalName = str_replace("'", "&apos;", $originalName);
                            $originalName = str_replace('"', '&quot;', $originalName);

                            $res .= "<a href='#' data-toggle='modal' data-target='#modalDirectDownload' onclick='editModalDirectDownload(".'"'."$path".'"'.", ".'"'."$key".'"'.", ".'"'.$originalName.'"'.")'><i class='fas fa-download edit'></i></a>\n";
                            $res .= "</td>\n";
                            $res .= "</tr>\n";
                        }
                        echo($res);
                        ?>
                    </table>
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
