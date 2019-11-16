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

                            <div class="form-group">
                                <label for="delete-fileid">Êtes-vous sûr de vouloir supprimer ce fichier ?</label>
                                <!-- visible seulement pour test-->
                                <input type="hidden" class="form-control" id="delete-fileid" name="delete-fileid" value="<?php echo("{ID}"); ?>" readonly/>
                            </div>

                            <br />
                            <br />

                            <input type="hidden" name="action" value="delete" />
                            <button type="submit "value="valider">Oui</button>

                            <div style="display: none;" id="hint_delete"></div>

                        </form>


                        <br />
                        <br />
                        
                        <div style="text-align: center;">
                            <button type="button" data-dismiss="modal">Annuler</button>
                        </div>


                    </div>

                    <div class="col-lg-2">

                    </div>