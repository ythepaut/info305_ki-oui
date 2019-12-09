<div class="modal fade" id="modalUploadFileError" tabindex="-1" role="dialog" aria-labelledby="modalUploadFileError" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">

            <div class="modal-body">

                <div class="container-fluid">
                <div class="row">

                    <div class="col-lg-12"  style="text-align: center;">
                        <h4 class="modal-title">VOUS N'AVEZ PLUS ASSEZ D'ESPACE !</h4>
                    </div>


                    <div class="col-lg-12"  style="text-align: center;">
                    

                        <p>Vous n'avez pas pu envoyer votre fichier car vous n'avez plus assez d'espace sur votre compte.</p>


                        <?php if ($_SESSION['Data']['quota'] < 5*10**9) { ?>

                            <br />

                            <h5>Augmentez votre espace à <b>5 Go</b> pour <b>2€/an</b> dès à present !</h5>
                            <br />

                            <button type="button" onclick="window.location.href='/espace-utilisateur/commande';">Augmenter mon plafond</button>

                        <?php } ?>


                    </div>

                </div>
                </div>
            </div>
        </div>
    </div>
</div>
