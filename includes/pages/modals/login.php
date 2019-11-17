
<div class="modal fade" id="modalLogin" tabindex="-1" role="dialog" aria-labelledby="modalLogin" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">

            <div class="modal-body">

                <div class="container-fluid" style="text-align: center;">
                <div class="row">

                    <div class="col-lg-12">
                        <h4 class="modal-title">CONNEXION</h4>
                    </div>
                    
                    <div class="col-lg-2">

                    </div>

                    <div class="col-lg-8">

                        <form action="<?php echo(getSrc('./includes/classes/actions.php')); ?>" method="POST" class="ajax">

                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text label-icon" id="icon_login_email"><i class="fas fa-at"></i></span>
                                </div>
                                <input type="email" name="login_email" class="form-control" placeholder="Adresse e-mail" aria-describedby="icon_login_email" required />
                            </div>
                            
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text label-icon" id="icon_login_passwd"><i class="fas fa-unlock"></i></span>
                                </div>
                                <input type="password" name="login_passwd" class="form-control" placeholder="Mot de passe" aria-describedby="icon_login_passwd" required />
                            </div>

                            <div class="row">
                                <div class="col-lg-6">
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" name="login_remember" id="login_remember" disabled />
                                        <label class="custom-control-label" for="login_remember">Se souvenir de moi <i class="fas fa-info-circle" title="Fortement déconseillé sur un ordinateur qui n'est pas le vôtre."></i></label>
                                    </div>
                                </div>

                                <div class="col-lg-6">
                                    <a href="#" data-dismiss="modal" data-toggle="modal" data-target="#modalForgotPwd" class="link">Mot de passe oublié</a>
                                </div>
                            </div>
                            <br />


                            <input type="hidden" name="action" value="login">
                            <input type="submit" value="Valider">


                        </form>
                    </div>

                    <div class="col-lg-2">

                    </div>


                    <div class="col-lg-12">

                        <div style="display: none;" id="hint_login"></div>
                        
                    </div>

                
                </div>
                </div>

            </div>

            <div class="modal-footer" style="background-color: #eaeaea; display: initial; text-align: center;">
                <span>Pas encore enregistré ? <a href="#" data-dismiss="modal" data-toggle="modal" data-target="#modalRegister" class="link">Créer un compte</a>.</span>
            </div>

        </div>

    </div>
</div>

