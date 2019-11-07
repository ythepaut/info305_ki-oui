<style>
	<?php include_once 'css/espace-utilisateur.css'; 
		  include_once '../classes/utils.php';
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
							$espace =recup_espace_occuper($_SESSION['Data']['username'],$connection);
                        ?>
                        <br/>
                        Espace restant:
                        <?php
                            $ESPACE_MAX=250*10**6;
                            echo(conversion_unitees($ESPACE_MAX-$espace));
                        ?>
                        <br/>
					</p>
					<p class="col-lg-3">
						Blocs ?(ajout fichier)
						Lorem ipsum dolor sit amet consectetur adipisicing elit. Adipisci dolores consequatur eveniet quos aperiam, molestiae facilis aliquid accusantium pariatur, accusamus, quidem cumque at illo exercitationem. Ut quos impedit quisquam eligendi!
					</p>
				</div>
				<div class="row">
					<table>
                    <tr>
                        <td>
                            Nom du fichier
                        </td>
                        <td>
                            Taille du fichier
                        </td>
                    </tr>
					<?php
						$fichiers=recup_fichiers($_SESSION['Data']['username'],$connection);
                        foreach($fichiers as $fichier){
                            echo("<tr>");
                                echo("<td>");
                                echo($fichier["original_name"]);
                                echo("</td>");
                                echo("<td>");
                                echo(conversion_unitees($fichier["size"]));
                                echo("</td>");
                            echo("</tr>");
                        }
                    ?>
                    </table>
				</div>
			</section>
		</div>
	</div>