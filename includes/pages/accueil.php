<noscript>
    <meta http-equiv="refresh" content="0;url=/no-script">
</noscript>

<section class="pair container-fluid">
	<div class="row ligne0" id="index-background">

		<div class="inner col-lg-5">
        <div id="titrecentre">

            <div class="d-lg-none">
                <h1 style="margin-top: 0.35rem;margin-bottom: 0px;">Partagez vos fichiers en toute sécurité.</h1>
            </div>

            <div class="d-none d-lg-block" style="display:flex;">
            <h1 style="margin-top: 0.35rem;margin-bottom: 0px;">Partagez vos fichiers </h1>
            <div class="animate-contain">
                <div class="animated-text">
                    <span> &nbsp;simplement.</span>
                    <span> &nbsp;en toute sécurité.</span>
                    <span> &nbsp;gratuitement.</span>
                </div>
            </div>
            </div>

            <br />
			<br />
			<p><button data-toggle="modal" data-target="#modalRegister"><i class="fas fa-pen"></i> &nbsp; S'inscrire</button></p>

        </div>
		</div>

		<div class="inner col-lg-1 d-none d-lg-block"></div>

	</div>
</section>



<section class="impair container-fluid">
	<div class="row ligne1">

		<div class="inner col-lg-4">
			<h2>Chiffrement de bout en bout</h2>
			<i class="fas fa-lock"></i>
			<p>Chez Ki-Oui, les données de vos fichiers sont cryptées.<br />Vous seul avez la main sur vos données.</p>
		</div>

        <div class="inner col-lg-4">
            <h2>Complet, mais simple d'utilisation</h2>
            <i class="fas fa-link"></i>
            <p>Un simple lien suffit pour partager vos fichiers.</p>
        </div>

		<div class="inner col-lg-4">
			<h2>Transparent</h2>
			<i class="fab fa-github-alt"></i>
			<p>Le code source de Ki-Oui est disponible.<br />Vous pouvez vérifier comment vos données sont sécurisées à tout moment.</p>
		</div>

	</div>
</section>



<section class="pair container-fluid">
	<div class="row ligne2">

		<div class="inner col-lg-4">
			<h2>Utilisateurs inscrits</h2>
			<span class="stats">
				<i class="far fa-user"></i>
				<?php
				echo(getNbUsers($connection));
				?>	
			</span>
		</div>

		<div class="inner col-lg-4">
			<h2>Taille totale</h2>
			<span class="stats">
				<?php
				echo(getNbSize($connection));
				?>
			</span>
		</div>

		<div class="inner col-lg-4">
			<h2>Fichiers stockés</h2>
			<span class="stats">
				<?php
				echo(getNbFiles($connection));
				?>	
			</span>
		</div>

	</div>
</section>