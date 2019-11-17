
<div class="modal fade" id="modalDeleteKnownDevices" tabindex="-1" role="dialog" aria-labelledby="modalDeleteKnownDevices" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">

            <div class="modal-body">

                <div class="container-fluid">
                <div class="row">

                    <div class="col-lg-12"  style="text-align: center;">
                        <h4 class="modal-title">SUPPRESSION DES APPAREILS ENREGISTRÉS</h4>
                    </div>
                    
                    <div class="col-lg-1">

                    </div>

                    <div class="col-lg">
                    <form action="<?php echo(getSrc('./includes/classes/actions.php')); ?>" method="POST" class="ajax" style="text-align: center;">
                        
                        <span>Êtes-vous sûr de vouloir supprimer tous vos appareils enregistrés ?</span>

                        <br />
                        <br />
                        

                        <input type="hidden" name="action" value="delete-known-devices">

                        <div style="text-align: center;">
                            
                            <input type="submit" value="Valider" />
                            <button data-dismiss="modal">Annuler</button>

                        </div>



                    </form>
                    </div>

                    <div class="col-lg-1">

                    </div>


                    <div class="col-lg-12">
                        <div style="display: none;" id="hint_delete-known-devices"></div>
                    </div>

                
                </div>
                </div>

            </div>


        </div>

    </div>
</div>

