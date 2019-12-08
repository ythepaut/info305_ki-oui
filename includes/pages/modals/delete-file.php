<div class="modal fade" id="modalDeleteFile" tabindex="-1" role="dialog" aria-labelledby="modalDeleteFile" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">

            <div class="modal-body">

                <div class="container-fluid">
                <div class="row">

                    <div class="col-lg-12"  style="text-align: center;">
                        <h4 class="modal-title">SUPPRESSION</h4>
                    </div>

                    <div class="col-lg-2">

                    </div>

                    <div class="col-lg-8">


                       <form action="<?php echo(getSrc('./includes/classes/actions.php')); ?>" method="POST" class="ajax" style="text-align: center;">

                        <span>Êtes-vous sûr de vouloir supprimer ce fichier ?</span>
                                
                            <input type="hidden" class="form-control" id="delete-fileid" name="delete-fileid" value="{ID}" />

                            <br />
                            <br />
                            <input type="hidden" name="action" value="delete" />
                            <input type="submit" value="Supprimer" name="Supprimer" />


                            <button type="button" data-dismiss="modal">Annuler</button>

                            <div style="display: none;" id="hint_delete"></div>

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
