
<div class="modal fade" id="modalForgotPwd" tabindex="-1" role="dialog" aria-labelledby="modalForgotPwd" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">

            <div class="modal-body">

                <div class="container-fluid" style="text-align: center;">
                <div class="row">

                    <div class="col-lg-12">
                        <h4 class="modal-title">REINITIALISATION DE MOT DE PASSE</h4>
                    </div>
                    
                    <div class="col-lg-2">

                    </div>

                    <div class="col-lg-8">

                        <form action="<?php echo(getSrc('./includes/classes/actions.php')); ?>" method="POST" class="ajax">

                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text label-icon" id="icon_forgot-pwd_email"><i class="fas fa-at"></i></span>
                                </div>
                                <input type="email" name="forgot-pwd_email" class="form-control" placeholder="Adresse e-mail" aria-describedby="icon_forgot-pwd_email" required />
                            </div>

                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text label-icon" id="icon_forgot-pwd_backup-key"><i class="fas fa-key"></i></span>
                                </div>
                                <input type="text" name="forgot-pwd_backup-key" class="form-control" placeholder="Clé de secours" aria-describedby="icon_forgot-pwd_backup-key" required />
                            </div>
                            
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text label-icon" id="icon_forgot-pwd_new-passwd"><i class="fas fa-unlock"></i></span>
                                </div>
                                <input type="password" name="forgot-pwd_new-passwd" class="form-control" placeholder="Nouveau mot de passe" aria-describedby="icon_forgot-pwd_new-passwd" required />
                            </div>
                            
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text label-icon" id="icon_forgot-pwd_new-passwd2"><i class="fas fa-unlock"></i></span>
                                </div>
                                <input type="password" name="forgot-pwd_new-passwd2" class="form-control" placeholder="Nouveau mot de passe (confirmation)" aria-describedby="icon_forgot-pwd_new-passwd2" required />
                            </div>

                            <br />


                            <input type="hidden" name="action" value="forgot-pwd">
                            <input type="submit" value="Valider">


                        </form>
                    </div>

                    <div class="col-lg-2">

                    </div>


                    <div class="col-lg-12">

                        <div style="display: none;" id="hint_forgot-pwd"></div>
                        
                    </div>

                
                </div>
                </div>

            </div>

            <div class="modal-footer" style="background-color: #eaeaea; display: initial; text-align: center;">
                <span>Vous avez votre mot de passe ? <a href="#" data-dismiss="modal" data-toggle="modal" data-target="#modalLogin" class="link">Se connecter</a>.</span>
            </div>

        </div>

    </div>
</div>

