
<div class="modal fade" id="modalUploadFileError" tabindex="-1" role="dialog" aria-labelledby="modalUploadFileError" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document"> <!-- modal-dialog-centered -->
        <div class="modal-content">
            <div class="modal-body">
                <div class="container-fluid" style="text-align: center;">
                    <div class="row">
                        <div class="col-lg-12">
                            <h4 class="modal-title"> Erreur </h4>
                        </div>

                        <div class="col-lg-12 align-self-center">
                            <h5> L'envoi est trop volumineux <br />
                                Espace restant : <?php echo(convertUnits($_SESSION["Data"]["quota"])); ?>
                            </h5>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
