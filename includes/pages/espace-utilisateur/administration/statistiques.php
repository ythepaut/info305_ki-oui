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
                            <p class="dash-heading"><?php echo("?"); ?></p>
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
                            <p class="dash-heading"><?php echo("?"); ?></p>
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


        <div class="card admin-events">

            <div class="card-body" style="padding: 0.75rem;">
                <h5 class="card-title" style="font-weight: bold; font-size:18px;"><i class="fas fa-folder-plus"></i>&nbsp; &nbsp;NOUVEAU FICHIER</h5>
                <span class="card-text">De <b>Jean michel</b></span> &nbsp;&nbsp; <span class='badge badge-secondary text-right'>+ 5 Mo</span>
            </div>

            <div class="card-footer" style="padding: 0.30rem 1.25rem;">
                <small class="text-muted">Il y a 2 minutes</small>
            </div>

        </div>

        <div class="card admin-events">

            <div class="card-body" style="padding: 0.75rem;">
                <h5 class="card-title" style="font-weight: bold; font-size:18px;"><i class="fas fa-folder-plus"></i>&nbsp; &nbsp;NOUVEAU FICHIER</h5>
                <span class="card-text">De <b>Cristina Cordula</b></span> &nbsp;&nbsp; <span class='badge badge-secondary text-right'>+ 27 Mo</span>
            </div>

            <div class="card-footer" style="padding: 0.30rem 1.25rem;">
                <small class="text-muted">Il y a 8 minutes</small>
            </div>

        </div>

        <div class="card admin-events-fullblue">

            <div class="card-body" style="padding: 0.75rem;">
                <h5 class="card-title" style="font-weight: bold; font-size:18px;"><i class="fas fa-user-plus"></i>&nbsp; &nbsp;NOUVEL UTILISATEUR</h5>
                <span class="card-text"><b>Bob le bricoleur</b></span>
            </div>

            <div class="card-footer" style="padding: 0.30rem 1.25rem;">
                <small class="text-muted">Il y a 2 heures</small>
            </div>

        </div>

        <div class="card admin-events">

            <div class="card-body" style="padding: 0.75rem;">
                <h5 class="card-title" style="font-weight: bold; font-size:18px;"><i class="fas fa-folder-plus"></i>&nbsp; &nbsp;NOUVEAU FICHIER</h5>
                <span class="card-text">De <b>Poney</b></span> &nbsp;&nbsp; <span class='badge badge-secondary text-right'>+ 7 Mo</span>
            </div>

            <div class="card-footer" style="padding: 0.30rem 1.25rem;">
                <small class="text-muted">Il y a 3 heures</small>
            </div>

        </div>

        <div class="card admin-events-red">

            <div class="card-body" style="padding: 0.75rem;">
                <h5 class="card-title" style="font-weight: bold; font-size:18px;"><i class="fas fa-folder-minus"></i>&nbsp; &nbsp;FICHIER SUPPRIMÉ</h5>
                <span class="card-text">Par <b>Poney</b></span> &nbsp;&nbsp; <span class='badge badge-secondary text-right'>- 78 Mo</span>
            </div>

            <div class="card-footer" style="padding: 0.30rem 1.25rem;">
                <small class="text-muted">Il y a 3 heures</small>
            </div>

        </div>

        <div class="card admin-events-fullred">

            <div class="card-body" style="padding: 0.75rem;">
                <h5 class="card-title" style="font-weight: bold; font-size:18px;"><i class="fas fa-user-minus"></i>&nbsp; &nbsp;COMPTE CLÔTURÉ</h5>
                <span class="card-text"><b>Francois Fillon</b></span>
            </div>

            <div class="card-footer" style="padding: 0.30rem 1.25rem;">
                <small class="text-muted">Il y a 9 heures</small>
            </div>

        </div>

        <div class="card admin-events">

            <div class="card-body" style="padding: 0.75rem;">
                <h5 class="card-title" style="font-weight: bold; font-size:18px;"><i class="fas fa-folder-plus"></i>&nbsp; &nbsp;NOUVEAU FICHIER</h5>
                <span class="card-text">De <b>Jean michel</b></span> &nbsp;&nbsp; <span class='badge badge-secondary text-right'>+ 751 Ko</span>
            </div>

            <div class="card-footer" style="padding: 0.30rem 1.25rem;">
                <small class="text-muted">Il y a 1 jour</small>
            </div>

        </div>

        <div class="card admin-events-red">

            <div class="card-body" style="padding: 0.75rem;">
                <h5 class="card-title" style="font-weight: bold; font-size:18px;"><i class="fas fa-folder-minus"></i>&nbsp; &nbsp;FICHIER SUPPRIMÉ</h5>
                <span class="card-text">Par <b>Cristina Cordula</b></span> &nbsp;&nbsp; <span class='badge badge-secondary text-right'>- 63 Ko</span>
            </div>

            <div class="card-footer" style="padding: 0.30rem 1.25rem;">
                <small class="text-muted">Il y a 1 jour</small>
            </div>

        </div>



    </div>



    <div class="col-lg-9 panel-outline-admin">

        <!--Graphique uploads/downloads/nb utilisateurs-->
        <div class="row" style="margin-bottom: 80px;">
            <div class="col chart-container" style="padding: 30px;">
                <canvas id="chart-js-stats1" class="chartjs"></canvas>
            </div>
            <script>
                
                const chart1 = document.querySelector("#chart-js-stats1");
                
                let lineChart = new Chart(chart1, {
                    type: 'line',
                    data: {
                        labels: ["January", "February", "March", "April", "May", "June", "July"],
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
                                data: [1, 2, 0, 5, 3, 5, 6],
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
                                data: [4, 8, 6, 12, 2, 5, 9],
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
                                data: [10, 12, 8, 4, 21, 12, 15],
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
                                data: [54, 37, 47, 42, 51, 40, 38],
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
