    	</main>
        </div>

    	<footer class="container-fluid">
    		<div class="footer row justify-content-center">
                <div class="col-lg-1">
                    &nbsp;
                </div>

                <div class="col-lg-3">
                    <h3>A PROPOS</h3>
                    <p>
                        KI-OUI est un outil de partage de fichiers simple et intuitif.<br />
                        Ce site internet a été réalisé par THEODET Romain, CARMAGNAC Christophe, CHARDONNET Lucas et THEPAUT Yohann dans le cadre du module INFO305 : Projet Web à l'Université Savoie Mont-Blanc.
                    </p>
                </div>

                <div class="col-lg-1">
                    &nbsp;
                </div>

                <div class="col-lg-2">
                    <h3>LIENS UTILES</h3>
                    <ul>
                        <li><a href="/">Accueil</a></li>
                        <li><a href="/espace-utilisateur">Espace utilisateur</a></li>
                        <li><a href="/mentions-legales">Mentions légales</a></li>
                        <li><a href="/cgu">Conditions génerales d'utilisation</a></li>
                    </ul>
                </div>

                <div class="col-lg-1">
                    &nbsp;
                </div>

                <div class="col-lg-3">
                    <h3>NOUS CONTACTER</h3>
                    <ul>
                        <li><a href="/nous-contacter">Formulaire de contact</a></li>
                        <li><a href="/espace-utilisateur/assistance">Assistance technique</a></li>
                        <li>Par e-mail à <a href="mailto:ki-oui@ythepaut.com">ki-oui@ythepaut.com</a></li>
                    </ul>
                </div>

                <div class="col-lg-1">
                    &nbsp;
                </div>

            </div>

            <span class="copy">Cette œuvre est mise à disposition selon les termes de la Licence Creative Commons « CC BY-NC-SA 4.0 » &nbsp; | &nbsp; <a href="https://github.com/ythepaut/info305_ki-oui" target="_blank">Code source <i style="position: relative; top:-.3em; font-size: 9px;" class="fas fa-external-link-alt"></i></a></span>

        </footer>

        <!--Ajax-->
        <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.0/jquery.min.js"></script>
        <script src="https://ajax.googleapis.com/ajax/libs/jqueryui/1.11.2/jquery-ui.min.js"></script>

        <!--Google re-captcha-->
        <script src="https://www.google.com/recaptcha/api.js?render=<?php echo($recaptcha['public']); ?>"></script>

        <!--Nos JS-->
        <script src="<?php echo(getSrc('./js/index.js')); ?>"></script>
        <script src="<?php echo(getSrc('./js/upload.js')); ?>"></script>
        <script src="<?php echo(getSrc('./js/ajax.js')); ?>"></script>
        <script src="<?php echo(getSrc('./js/tts.js')); ?>"></script>

        <!--FontAwesome-->
        <script src="https://kit.fontawesome.com/902b444792.js" crossorigin="anonymous"></script>

        <!--Bootstrap-->
        <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.0/dist/umd/popper.min.js" integrity="sha384-Q6E9RHvbIyZFJoft+2mJbHaEWldlvI9IOYy5n3zV9zzTtmI3UksdQRVvoxMfooAo" crossorigin="anonymous"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.4.0/js/bootstrap.min.js" integrity="sha384-3qaqj0lc6sV/qpzrc1N5DC6i1VRn/HyX4qdPaiEFbn54VjQBEU341pvjz7Dv3n6P" crossorigin="anonymous"></script>
        </div>
    </body>
</html>
