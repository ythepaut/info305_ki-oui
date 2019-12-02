<!-- Chiffres -->
<div class="row">

    <div class="col-lg-3">
        <div class="card">
                <div class="card-heading">
                    <div class="row">
                        <div class="col-lg-3">
                            <i class="fas fa-users" style="margin:20px;font-size:60px;"></i>
                        </div>
                        <div class="col-lg-9 text-right">
                            <p class="dash-heading"><?php echo(getNbUsers($connection)); ?></p>
                            <p class="dash-text">Utilisateurs enregistrés</p>
                        </div>
                    </div>
                </div>
                
            </div>
    </div>

    <div class="col-lg-3">
        <div class="card">
                <div class="card-heading">
                    <div class="row">
                        <div class="col-lg-3">
                            <i class="fas fa-file" style="margin:20px;font-size:60px;"></i>
                        </div>
                        <div class="col-lg-9 text-right">
                            <p class="dash-heading"><?php echo(getNbFiles($connection)); ?></p>
                            <p class="dash-text">Fichiers stockés</p>
                        </div>
                    </div>
                </div>
                
            </div>
    </div>

    <div class="col-lg-3">
        <div class="card">
                <div class="card-heading">
                    <div class="row">
                        <div class="col-lg-3">
                            <i class="fas fa-upload" style="margin:20px;font-size:60px;"></i>
                        </div>
                        <div class="col-lg-9 text-right">
                            <p class="dash-heading"><?php echo(getStats("uploadCount", $connection)); ?></p>
                            <p class="dash-text">Fichiers envoyés</p>
                        </div>
                    </div>
                </div>
                
            </div>
    </div>

    <div class="col-lg-3">
        <div class="card">
                <div class="card-heading">
                    <div class="row">
                        <div class="col-lg-3">
                            <i class="fas fa-download" style="margin:20px;font-size:60px;"></i>
                        </div>
                        <div class="col-lg-9 text-right">
                            <p class="dash-heading"><?php echo(getStats("downloadCount", $connection)); ?></p>
                            <p class="dash-text">Fichiers téléchargés</p>
                        </div>
                    </div>
                </div>
                
            </div>
    </div>

</div>

<!-- Actions récentes et Graphiques -->
<div class="row">

    <!--Actions récentes-->
    <div class="col-lg" style="margin: 20px 15px; padding: 0px;">

        <?php

        $eventArray = json_decode(getStats("recentEvents", $connection), true);
        
        foreach ($eventArray as $event) {
            $icon = "";
            $title = "";
            $desc = "";
            switch ($event['type']) {
                case "ACCOUNT_CREATION":
                    $icon = "fas fa-user-plus";
                    $class = "card admin-events-fullblue";
                    $title = "NOUVEL UTILISATEUR";
                    $desc = "<span class='card-text'><b>" . $event['user'] . "</b></span>";
                    break;
                case "ACCOUNT_DELETION":
                    $icon = "fas fa-user-minus";
                    $class = "card admin-events-fullred";
                    $title = "COMPTE CLÔTURÉ";
                    $desc = "<span class='card-text'><b>" . $event['user'] . "</b></span>";
                    break;
                case "FILE_UPLOAD":
                    $icon = "fas fa-folder-plus";
                    $class = "card admin-events";
                    $title = "NOUVEAU FICHIER";
                    $desc = "<span class='card-text'>De <b>" . $event['user'] . "</b></span> &nbsp;&nbsp; <span class='badge badge-secondary text-right'>" . $event['badge'] . "</span>";
                    break;
                case "FILE_DELETE":
                    $icon = "fas fa-folder-minus";
                    $class = "card admin-events-red";
                    $title = "FICHIER SUPPRIMÉ";
                    $desc = "<span class='card-text'>Par <b>" . $event['user'] . "</b></span> &nbsp;&nbsp; <span class='badge badge-secondary text-right'>" . $event['badge'] . "</span>";
                    break;
            }
            ?>

            <div class="<?php echo($class); ?>">

                <div class="card-body" style="padding: 0.75rem;">
                    <h5 class="card-title" style="font-weight: bold; font-size:18px;"><i class="<?php echo($icon); ?>"></i>&nbsp; &nbsp;<?php echo($title); ?></h5>
                    <?php echo($desc); ?>
                </div>

                <div class="card-footer" style="padding: 0.30rem 1.25rem;">
                    <small class="text-muted"><?php echo(time_elapsed_string("@" . $event['timestamp'])); ?></small>
                </div>

            </div>

            <?php
        }
        
        ?>


    </div>



    <div class="col-lg-9 panel-outline-admin">

        <!--Graphique uploads/downloads/nb utilisateurs-->
        <div class="row" style="margin-bottom: 80px;">
            <div class="col chart-container" style="padding: 30px;">
                <canvas id="chart-js-stats1" class="chartjs"></canvas>
            </div>

            <script>

                <?php
                $logArray = json_decode(getStats("newUserLog", $connection), true);
                $labels = "[";
                $dataNewUsers = "[";
                foreach ($logArray as $label => $value) {
                    $labels .= "\"" . $label . "\"" . ",";
                    $dataNewUsers .= $value . ",";
                }
                $labels = substr($labels, 0, strlen($labels)-1) . "]";
                $dataNewUsers = substr($dataNewUsers, 0, strlen($dataNewUsers)-1) . "]";

                $logArray = json_decode(getStats("reconnectUserLog", $connection), true);
                $dataReconUsers = "[";
                foreach ($logArray as $value) {
                    $dataReconUsers .= $value . ",";
                }
                $dataReconUsers = substr($dataReconUsers, 0, strlen($dataReconUsers)-1) . "]";

                $logArray = json_decode(getStats("uploadLog", $connection), true);
                $dataUpload = "[";
                foreach ($logArray as $value) {
                    $dataUpload .= $value . ",";
                }
                $dataUpload = substr($dataUpload, 0, strlen($dataUpload)-1) . "]";

                $logArray = json_decode(getStats("downloadLog", $connection), true);
                $dataDownload = "[";
                foreach ($logArray as $value) {
                    $dataDownload .= $value . ",";
                }
                $dataDownload = substr($dataDownload, 0, strlen($dataDownload)-1) . "]";
                ?>
                
                const chart1 = document.querySelector("#chart-js-stats1");
                
                let lineChart = new Chart(chart1, {
                    type: 'line',
                    data: {
                        labels: <?php echo($labels); ?>,
                        datasets: [
                            {
                                label: "Nouveaux utilisateurs",
                                fill: false,
                                lineTension: 0.3,
                                backgroundColor: "rgba(0,0,0,0)",
                                borderColor: "#00cec9",
                                borderCapStyle: 'butt',
                                borderDash: [5,5],
                                borderDashOffset: 0.0,
                                borderJoinStyle: 'miter',
                                pointStyle: 'circle',
                                pointBorderColor: "#00cec9",
                                pointBackgroundColor: "#fff",
                                pointBorderWidth: 2,
                                pointHoverRadius: 5,
                                pointHoverBackgroundColor: "#00cec9",
                                pointHoverBorderColor: "#00cec9",
                                pointHoverBorderWidth: 2,
                                pointRadius: 5,
                                pointHitRadius: 20,
                                data: <?php echo($dataNewUsers); ?>,
                                spanGaps: false,
                            },
                            {
                                label: "Reconnexion d'utilisateurs",
                                fill: false,
                                lineTension: 0.3,
                                backgroundColor: "rgba(0,0,0,0)",
                                borderColor: "#a29bfe",
                                borderCapStyle: 'butt',
                                borderDash: [5,5],
                                borderDashOffset: 0.0,
                                borderJoinStyle: 'miter',
                                pointStyle: 'circle',
                                pointBorderColor: "#a29bfe",
                                pointBackgroundColor: "#fff",
                                pointBorderWidth: 2,
                                pointHoverRadius: 5,
                                pointHoverBackgroundColor: "#a29bfe",
                                pointHoverBorderColor: "#a29bfe",
                                pointHoverBorderWidth: 2,
                                pointRadius: 5,
                                pointHitRadius: 20,
                                data: <?php echo($dataReconUsers); ?>,
                                spanGaps: false,
                            },
                            {
                                label: "Fichier téléversés",
                                fill: false,
                                lineTension: 0.3,
                                backgroundColor: "rgba(0,0,0,0)",
                                borderColor: "#ff7675",
                                borderCapStyle: 'butt',
                                borderDash: [],
                                borderDashOffset: 0.0,
                                borderJoinStyle: 'miter',
                                pointStyle: 'circle',
                                pointBorderColor: "#ff7675",
                                pointBackgroundColor: "#fff",
                                pointBorderWidth: 2,
                                pointHoverRadius: 5,
                                pointHoverBackgroundColor: "#ff7675",
                                pointHoverBorderColor: "#ff7675",
                                pointHoverBorderWidth: 2,
                                pointRadius: 5,
                                pointHitRadius: 20,
                                data: <?php echo($dataUpload); ?>,
                                spanGaps: false,
                            },
                            {
                                label: "Fichier téléchargés",
                                fill: false,
                                lineTension: 0.3,
                                backgroundColor: "rgba(0,0,0,0)",
                                borderColor: "#fd79a8",
                                borderCapStyle: 'butt',
                                borderDash: [],
                                borderDashOffset: 0.0,
                                borderJoinStyle: 'miter',
                                pointStyle: 'circle',
                                pointBorderColor: "#fd79a8",
                                pointBackgroundColor: "#fff",
                                pointBorderWidth: 2,
                                pointHoverRadius: 5,
                                pointHoverBackgroundColor: "#fd79a8",
                                pointHoverBorderColor: "#fd79a8",
                                pointHoverBorderWidth: 2,
                                pointRadius: 5,
                                pointHitRadius: 20,
                                data: <?php echo($dataDownload); ?>,
                                spanGaps: false,
                            }
                        ]
                    },
                    options: {
                        scales: {
                            xAxes: [{
                                display: true
                            }]
                        }
                    }
                });

            </script>
        </div>



        <!--Fidélisation-->

        <div class="row">
            <div class="col-lg-6 chart-container">
                <canvas id="chart-js-stats2" width="585" height="293" class="chartjs"></canvas>
            </div>
            <div class="col-lg-6 chart-container">
                <canvas id="chart-js-stats3" width="585" height="293" class="chartjs"></canvas>
            </div>
            <script>


                var options = {
                    maintainAspectRatio: false,
                    rotation: 2 * Math.PI,
                    circumference: 2 * Math.PI,
                    cutoutPercentage: 80,
                    legend: {
                        display: true,
                        position: 'top',
                        fullWidth: false,
                        onClick: null
                    }
                };
                var data2 = {
                    datasets: [{
                        data:[21,79],
                        backgroundColor:["#fdcb6e", "#00b894"],
                        weight: 15,
                    }],
                    labels: [
                        'Utilisateurs ponctuels (%)',
                        'Utilisateurs recurrents (%)'
                    ]
                };
                new Chart(document.querySelector("#chart-js-stats2"),{
                    type: 'doughnut',
                    data: data2,
                    options: options,
                });

                var data3 = {
                    datasets: [{
                        data:[7,93],
                        backgroundColor:["#74b9ff", "#a29bfe"],
                        weight: 15,
                    }],
                    labels: [
                        'Utilisateurs parrains (%)',
                        'Utilisateurs parrainés (%)'
                    ]
                };
                new Chart(document.querySelector("#chart-js-stats3"),{
                    type: 'doughnut',
                    data: data3,
                    options: options,
                });

            </script>

        </div>



        
    </div>
        
</div>
