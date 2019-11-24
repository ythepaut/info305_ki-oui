
<div class="modal fade" id="modalChangeEmail" tabindex="-1" role="dialog" aria-labelledby="modalChangeEmail" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">

            <div class="modal-body">

                <div class="container-fluid">
                <div class="row">

                    <div class="col-lg-12"  style="text-align: center;">
                        <h4 class="modal-title">CHANGEMENT D'ADRESSE E-MAIL</h4>
                    </div>
                    
                    <div class="col-lg-2">

                    </div>

                    <div class="col-lg-8">

                        <form action="<?php echo(getSrc('./includes/classes/actions.php')); ?>" method="POST" class="ajax">
                        

                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text label-icon" id="icon_change-email_newEmail"><i class="fas fa-at"></i></span>
                                </div>
                                <input type="email" name="change-email_newEmail" class="form-control" placeholder="Nouvelle adresse e-mail" aria-describedby="icon_change-email_newEmail" required />
                            </div>

                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text label-icon" id="icon_change-email_password"><i class="fas fa-key"></i></span>
                                </div>
                                <input type="password" name="change-email_password" class="form-control" placeholder="Mot de passe requis pour confirmer votre action" aria-describedby="icon_change-email_password" required />
                            </div>


                            <input type="hidden" name="action" value="change-email" />
                            <div style="text-align: center;">
                                <input type="submit" value="Valider" />
                            </div>

                        </form>


                    </div>

                    <div class="col-lg-2">

                    </div>


                    <div class="col-lg-12">

                        <div style="display: none;" id="hint_change-email"></div>
                        
                    </div>


                
                </div>
                </div>

            </div>


        </div>

    </div>
</div>

