<div class="container-fluid">
    <div class="row">
        <?php include("./includes/pages/espace-utilisateur/nav-utilisateur.php"); ?>

        <section class="col-lg-10 panel-background">

        <div class="row">

            <div class="col-lg-2">
            </div>

            <div class="col-lg-8 panel-outline">
                    
                <h4 class="panel-title">VERIFICATION À DOUBLE FACTEUR</h4>

                <?php if ($_SESSION['tfa'] == "totp") { ?>

                    <form action="<?php echo(getSrc('./includes/classes/actions.php')); ?>" method="POST" class="ajax">
                    <div class="row">
                        <div class="col-lg-1">
                        </div>

                        <div class="col-lg">

                            <span>La double authentification par application est activée sur ce compte.</span><br />
                            <span>Veuillez confirmer votre identité en saisissant votre code d'application TOTP ci-dessous.</span>

                            <br />
                            <br />

                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text label-icon" id="icon_totp_code"><i class="fas fa-key"></i></span>
                                </div>
                                <input type="text" class="form-control" aria-describedby="icon_totp_code" name="validate-totp_code" placeholder="Code TOTP" maxlength="6" required />
                            </div>
                            
                            <br />
                            
                            <input type="hidden" name="action" value="validate-totp" />

                            <div style="text-align: center;">
                                <input type="submit" value="Valider" />
                            </div>
                        
                        </div>

                        <div class="col-lg-1">
                        </div>
                    </div>

                    </form>
                    <div style="display: none;" id="hint_validate-totp"></div>
                    <br />

                <?php } elseif ($_SESSION['tfa'] == "new_device") { ?>

                    <form action="<?php echo(getSrc('./includes/classes/actions.php')); ?>" method="POST" class="ajax">
                    <div class="row">
                        <div class="col-lg-1">
                        </div>

                        <div class="col-lg">

                            <span>Nous ne reconnaissons pas cet appareil. Pour votre sécurité, veuillez vérifier votre boîte e-mail <b><?php echo($_SESSION['Data']['email']); ?></b>, et saisir le code pour completer votre connexion.</span><br />

                            <br />
                            <br />

                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text label-icon" id="icon_tfa_code"><i class="fas fa-key"></i></span>
                                </div>
                                <input type="text" class="form-control" aria-describedby="icon_tfa_code" name="validate-tfa_code" placeholder="Code reçu par e-mail" maxlength="6" required />
                            </div>
                            
                            <br />
                            
                            <input type="hidden" name="action" value="validate-tfa" />

                            <div style="text-align: center;">
                                <input type="submit" value="Valider" />
                            </div>
                        
                        </div>

                        <div class="col-lg-1">
                        </div>
                    </div>

                    </form>
                    <div style="display: none;" id="hint_validate-tfa"></div>

                    <form action="<?php echo(getSrc('./includes/classes/actions.php')); ?>" method="POST" class="ajax">
                    <div class="row">
                        <div class="col-lg-1">
                        </div>
                        <div class="col-lg" style="text-align: center;">
                            <input type="hidden" name="action" value="resend-tfa" />

                            <div style="text-align: center;">
                                <input type="submit" name="Renvoyer mon code" value="Renvoyer mon code" />
                            </div>

                        </div>
                        <div class="col-lg-1">
                        </div>
                    </div>
                    </form>
                    <div style="display: none;" id="hint_resend-tfa"></div>
                    <br />


                <?php } ?>

            </div>

            <div class="col-lg-2">
            </div>

        </div>



        </section>

    </div>

</div>
