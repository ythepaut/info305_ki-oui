
<div class="modal fade" id="modalRegister" tabindex="-1" role="dialog" aria-labelledby="modalRegister" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-body">

                <form action="<?php echo(getSrc('./includes/classes/actions.php')); ?>" method="POST" class="ajax">

                <div class="container-fluid" style="text-align: center;">
                <div class="row">

                    <div class="col-lg-12">
                        <h4 class="modal-title">INSCRIPTION</h4>
                    </div>

                    <div class="col-lg-6">

                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text label-icon" id="icon_register_username"><i class="fas fa-hashtag"></i></span>
                            </div>
                            <input type="text" name="register_username" class="form-control" placeholder="Nom d'utilisateur" aria-describedby="icon_register_username" required />
                        </div>

                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text label-icon" id="icon_register_email"><i class="fas fa-at"></i></span>
                            </div>
                            <input type="email" name="register_email" class="form-control" placeholder="Adresse e-mail" aria-describedby="icon_register_email" required />
                        </div>

                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text label-icon" id="icon_register_passwd"><i class="fas fa-unlock"></i></span>
                            </div>
                            <input type="password" id="passwdhint" name="register_passwd" class="form-control" placeholder="Mot de passe" aria-describedby="icon_register_passwd" tabindex="0" data-trigger="focus" data-container="body" data-toggle="popover" data-placement="bottom" title="Sécurité des mots de passes" data-content="Mot de passe de 8 caractères ou plus, avec au moins une lettre et un chiffre requis." required />
                        </div>

                    </div>

                    <div class="col-lg-6">

                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text label-icon" id="icon_register_passwd2"><i class="fas fa-unlock"></i></span>
                            </div>
                            <input type="password" name="register_passwd2" class="form-control" placeholder="Mot de passe (confirmation)" aria-describedby="icon_register_passwd2" required />
                        </div>

                        <div style="text-align: left;">
                            <ul style="list-style-type: none; margin: 5px 0px;">
                                <li>
                                    <div class="custom-control custom-checkbox">
                                        <input type="checkbox" class="custom-control-input" name="register_cgu" id="register_cgu" required />
                                        <label class="custom-control-label" for="register_cgu">En cochant cette case, j'affirme avoir lu, compris, accepté et appris par cœur les <a target="_blank" href="/cgu" class="link">Conditions Générales d'Utilisation <i style="position: relative; top:-.3em; font-size: 9px;" class="fas fa-external-link-alt"></i></a> et la <a target="_blank" href="/cgu" class="link">Politique de confidentialité <i style="position: relative; top:-.3em; font-size: 9px;" class="fas fa-external-link-alt"></i></a>.</label>
                                    </div>
                                </li>
                            </ul>
                        </div>


                    </div>


                    <div class="col-lg-12">

                        <input type="hidden" name="action" value="register">
                        <input type="hidden" name="register_recaptchatoken" class="recaptcha" value="recaptcha">
                        <input type="submit" value="Valider">

                        <div style="display: none;" id="hint_register"></div>
                        
                    </div>


                </div>
                </div>
                </form>

            </div>



            <div class="modal-footer" style="background-color: #eaeaea; display: initial; text-align: center;">
                <span>Vous avez déjà un compte ? <a href="#" data-dismiss="modal" data-toggle="modal" data-target="#modalLogin" class="link">Se connecter</a>.</span>
            </div>

        </div>
    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    $('#passwdhint').popover();
})
</script>