
<div class="modal fade" id="modalLogin" tabindex="-1" role="dialog" aria-labelledby="modalLogin" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">

            <div class="modal-body">

                <div class="container-fluid" style="text-align: center;">
                <div class="row">

                    <div class="col-lg-12">
                        <h4 class="modal-title">CONNEXION</h4>
                    </div>
                    
                    <div class="col-lg-6">

                        <form action="./includes/classes/actions.php" method="POST" class="ajax">

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

                            <input type="hidden" name="action" value="login">
                            <input type="submit" value="Valider">


                        </form>
                    </div>

                    <div class="col-lg-6">

                        <div class="g-signin2" data-onsuccess="onSignIn" data-theme="light"></div>
                        
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

