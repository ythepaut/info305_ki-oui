
<div class="modal fade" id="modalBackupKey" tabindex="-1" role="dialog" aria-labelledby="modalBackupKey" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">

            <div class="modal-body">

                <div class="container-fluid">
                <div class="row">

                    <div class="col-lg-12"  style="text-align: center;">
                        <h4 class="modal-title">CLÉ DE RÉCUPERATION GÉNÉRÉE</h4>
                    </div>
                    
                    <div class="col-lg-2">

                    </div>

                    <div class="col-lg-8">

                        <form action="<?php echo(getSrc('./includes/classes/actions.php')); ?>" method="POST" class="ajax">
                        
                            <span>Voici votre clé de secours :</span>
                            <br />
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text label-icon" id="icon_login_backupkey"><i class="fas fa-key"></i></span>
                                </div>
                                <input type="text" class="form-control" aria-describedby="icon_login_backupkey" name="backup-key_key" value="<?php echo($backupKey); ?>" disabled />
                            </div>
                            <br />
                            <span><b>ATTENTION</b>, il s'agit de la seule façon de récuperer vos fichiers si vous oubliez votre mot de passe.<br />Conservez-la dans un endroit sûr, et ne la perdez pas.</span>
                            
                            <br />
                            <br />
                            
                            <input type="hidden" name="action" value="backup-key" />
                            <input type="submit" value="J'ai noté ma clé" />

                        </form>


                    </div>

                    <div class="col-lg-2">

                    </div>


                    <div class="col-lg-12">

                        <div style="display: none;" id="hint_backup-key"></div>
                        
                    </div>


                
                </div>
                </div>

            </div>


        </div>

    </div>
</div>

