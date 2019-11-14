<div class="container-fluid">
    <div class="row">
        <?php include("./includes/pages/espace-utilisateur/nav-utilisateur.php"); ?>

        <section class="col-lg-10">
            <div class="row">
                <div class="col-lg-9">
                    <p>
                        Espace occupé par vos fichiers :
                        <?php
                            $size =getSize($_SESSION['Data']['id'],$connection);
                            echo(convertUnits($size));
                        ?>
                        <div class="chart-container">
                            <canvas id="chart-js-1" class="chartjs"></canvas>
                        </div>
                        <script>
                            var options = {
                                maintainAspectRatio: false,
                            };
                            //variable contenant l'espace utilisé par l'utilisateur
                            var sizeUser="<?php echo(getSize($_SESSION['Data']['id'],$connection));?>";
                            var data = {
                                datasets: [{
                                    data:[sizeUser,200*10**6-sizeUser],
                                    backgroundColor:["rgb(255,0,0)","rgb(0,0,255)"],
                                }],
                                labels: [
                                    'Espace Utilisé',
                                    'Espace Restant'
                                ]
                            };

                            new Chart(document.getElementById("chart-js-1"),{
                                type: 'pie',
                                data: data,
                                options: options,
                            });
                        </script>
                    </p>
                </div>
                <div class="col-lg-3 inner">
                        <a href="/ajout" class="button"><i class="fas fa-file-import"></i> &nbsp; Ajouter un fichier</a>
                </div>
            </div>
            <div class="row">
                <div class="col">
                    <table class="table">
                    <thead class="thead-light">
                    <th style="width=60%;">Nom du fichier</th>
                    <th style="width=20%;">Taille du fichier</th>
                    <th style="width=auto;">Actions</th>
                    </thead>
                        <?php
                            $folders=getFolders($_SESSION['Data']['id'],$connection);
                            $res="";
                            foreach($folders as $folder){
                                $res.="<tr>";
                                $res.="<td>";
                                $res.=$folder["original_name"];
                                $res.="</td>";
                                $res.="<td>";
                                $res.=convertUnits($folder["size"]);
                                $res.="</td>";
                                $res.="<td>";
                                $res.="<a href='#' data-toggle='modal' data-target='#modalDlLink' onclick='editModalDownload(\"" . generateDlLink($_SESSION['UserPassword'], $folder['id'], $connection) . "\")'><i class='fas fa-link edit'></i></a>";
                                $res.="</td>";
                                $res.="</tr>";
                            }
                            echo($res);
                        ?>
                    </table>
                </div>
            </div>
        </section>
    </div>
</div>

<?php include("./includes/pages/modals/dl-link.php"); ?>