<div class="modal fade" id="modalChangeSalt" tabindex="-1" role="dialog" aria-labelledby="modalChangeSalt" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">

            <div class="modal-body">

                <div class="container-fluid">
                <div class="row">

                    <div class="col-lg-12"  style="text-align: center;">
                        <h4 class="modal-title">REGÉNÉRER LE LIEN DE PARTAGE</h4>
                    </div>

                    <div class="col-lg-2">

                    </div>

                    <div class="col-lg-8">


                       <form action="<?php echo(getSrc('./includes/classes/actions.php')); ?>" method="POST" class="ajax" style="text-align: center;">

                        <span>Êtes-vous sûr de vouloir changer la clé de ce fichier ?</span><br />
                        <span>L'ancien lien ne sera plus utilisable.</span>
                        <br />
                                
                            <input type="hidden" class="form-control" id="change_salt-fileid" name="change_salt-fileid" value="{ID}" />

                            <br />
                            <input type="hidden" name="action" value="change-file-salt" />
                            <input type="submit" value="Modifier" name="Modifier" />


                            <button type="button" data-dismiss="modal">Annuler</button>

                            <div style="display: none;" id="hint_change-file-salt"></div>

                        </form>


                    </div>

                    <div class="col-lg-2">

                    </div>
                </div>
                </div>
            </div>
        </div>
    </div>
</div>