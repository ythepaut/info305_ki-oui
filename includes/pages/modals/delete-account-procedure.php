
<div class="modal fade" id="modalDeleteAccountProcedure" tabindex="-1" role="dialog" aria-labelledby="modalDeleteAccountProcedure" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">

            <div class="modal-body">

                <div class="container-fluid">
                <div class="row">

                    <div class="col-lg-12"  style="text-align: center;">
                        <h4 class="modal-title">FERMETURE DU COMPTE</h4>
                    </div>
                    
                    <div class="col-lg-1">

                    </div>

                    <div class="col-lg">
                    <form action="<?php echo(getSrc('./includes/classes/actions.php')); ?>" method="POST" class="ajax">
                        
                        <p><b>Êtes-vous sûr de vouloir clôturer votre compte ?</b><br /><br />
                        En cliquant sur "Valider", votre compte sera supprimé dans 15 jours.<br />
                        Vous pouvez annuler la procedure de suppression en vous reconnectant à votre compte dans ce delai.<br />
                        Vous recevrez une alerte à J-1. Une fois le compte supprimé, toutes vos données seront irrécuperables.</p>

                        <br />
                        
                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text label-icon" id="icon_delete-account-procedure_passwd"><i class="fas fa-unlock"></i></span>
                            </div>
                            <input type="password" name="delete-account-procedure_passwd" class="form-control" placeholder="Mot de passe requis pour confirmer votre action" aria-describedby="icon_delete-account-procedure_passwd" required />
                        </div>

                        <br />

                        <input type="hidden" name="action" value="delete-account-procedure">

                        <div style="text-align: center;">
                            
                            <input type="submit" value="Valider" />
                            <button data-dismiss="modal">Annuler</button>

                        </div>



                    </form>
                    </div>

                    <div class="col-lg-1">

                    </div>


                    <div class="col-lg-12">
                        <div style="display: none;" id="hint_delete-account-procedure"></div>
                    </div>

                
                </div>
                </div>

            </div>


        </div>

    </div>
</div>

