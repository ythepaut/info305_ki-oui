<style>
	<?php include_once 'css/espace-utilisateur.css'; 
		  include_once './includes/classes/utils.php';
	?>
</style>

	<div class="container-fluid">
		<div class="row">
			<div class="col-lg-2 coter">
				<table>
					<tr><td><a href="">Tableau de bord</a></td></tr>
					<tr><td><a href="">Mon compte</a></td></tr>
					<tr><td><a href="">Aide</a></td></tr>
				</table>
			</div>

			<section class="col-lg-10">
				<div class="row">
					<p class="col-lg-9">
						Espace occupée par vos fichiers:
						<?php 
							$espace =recup_espace_occuper($_SESSION['Data']['username']);
							echo($espace);
						?>
					</p>
					<p class="col-lg-3">
						Blocs ?(ajout fichier)
						Lorem ipsum dolor sit amet consectetur adipisicing elit. Adipisci dolores consequatur eveniet quos aperiam, molestiae facilis aliquid accusantium pariatur, accusamus, quidem cumque at illo exercitationem. Ut quos impedit quisquam eligendi!
					</p>
				</div>
				<div class="row">
					Fichiers de l'utilisateur
					<?php
						if (isset($_SESSION['LoggedIn'])){
							if ($_SESSION['LoggedIn']){
								echo("l'utilisateur est la!");
							}
							else{
								echo("l'utilisateur n'est po la :(");
								var_dump($_SESSION['LoggedIn']);
								echo("session:");
								var_dump($_SESSION);
							}
						}
						else{
							echo("la variable n'existe pas!");
							var_dump($_SESSION);
						}
					?>
				</div>
			</section>
		</div>
	</div>