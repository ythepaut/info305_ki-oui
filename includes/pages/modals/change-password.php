
<div class="modal fade" id="modalChangePassword" tabindex="-1" role="dialog" aria-labelledby="modalChangePassword" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">

            <div class="modal-body">

                <div class="container-fluid">
                <div class="row">

                    <div class="col-lg-12"  style="text-align: center;">
                        <h4 class="modal-title">CHANGEMENT DE MOT DE PASSE</h4>
                    </div>
                    
                    <div class="col-lg-2">

                    </div>

                    <div class="col-lg-8">

                        <form action="<?php echo(getSrc('./includes/classes/actions.php')); ?>" method="POST" class="ajax">
                        
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text label-icon" id="icon_change-password_oldpassword"><i class="fas fa-key"></i></span>
                                </div>
                                <input type="password" name="change-password_oldPassword" class="form-control" placeholder="Ancien mot de passe" aria-describedby="icon_change-password_oldpassword" required />
                            </div>
                            
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text label-icon" id="icon_change-password_newpassword"><i class="fas fa-key"></i></span>
                                </div>
                                <input type="password" name="change-password_newPassword" class="form-control" placeholder="Nouveau mot de passe" aria-describedby="icon_change-password_newpassword" required />
                            </div>

                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text label-icon" id="icon_change-password_confirmnewpassword"><i class="fas fa-key"></i></span>
                                </div>
                                <input type="password" name="change-password_newPasswordBis" class="form-control" placeholder="Réécriver le nouveau mot de passe" aria-describedby="icon_change-password_confirmnewpassword" required />
                            </div>

                            <input type="hidden" name="action" value="change-password" />
                            <div style="text-align: center;">
                            <input type="submit" value="Changer le mot de passe" />
                            </div>

                        </form>


                    </div>

                    <div class="col-lg-2">

                    </div>


                    <div class="col-lg-12">

                        <div style="display: none;" id="hint_change-password"></div>
                        
                    </div>


                
                </div>
                </div>

            </div>


        </div>

    </div>
</div>

