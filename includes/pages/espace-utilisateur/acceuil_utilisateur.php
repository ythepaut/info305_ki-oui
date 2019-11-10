	<div class="container-fluid">
		<div class="row">
			<div class="col-lg-2 coter">
				<ul class="white">
					<li><a href="/espace-utilisateur">Tableau de bord</a></li>
					<li><a href="/compte-utilisateur">Mon compte</a></li>
					<li><a href="/aide-utilisateur">Aide</a></li>
				</ul>
			</div>

			<section class="col-lg-10">
				<div class="row">
					<div class="col-lg-9">
						<p>
							Espace occupée par vos fichiers:
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
								//variable contenant l'espace utiliser par l'utilisateur
								var sizeUser="<?php echo(getSize($_SESSION['Data']['id'],$connection));?>";
								var data = {
									datasets: [{
										data:[sizeUser,200*10**6-sizeUser],
										backgroundColor:["rgb(255,0,0)","rgb(0,0,255)"],
									}],
									labels: [
										'Espace Utiliser',
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
						<table>
							<tr><td class="head">Nom Fichier</td><td class="head">Taille du fichier</td></tr>
							<?php
								$folders=getFolders($_SESSION['Data']['id'],$connection);
		                        foreach($folders as $folder){
		                            echo("<tr>");
		                                echo("<td>");
		                                echo($folder["original_name"]);
		                                echo("</td>");
		                                echo("<td>");
		                                echo(convertUnits($folder["size"]));
		                                echo("</td>");
		                            echo("</tr>");
		                        }
							?>
						</table>
					</div>
				</div>
			</section>
		</div>
	</div>
