<div class="container-fluid">
    <div class="row">
        <?php include("./includes/pages/espace-utilisateur/nav-utilisateur.php"); ?>

        <section class="col-lg-10 panel-background">

        <div class="row">

            <div class="col-lg-2">
            </div>

            <div class="col-lg-8 panel-outline">
                    
                <h4 class="panel-title">VERIFICATION À DOUBLE FACTEUR</h4>

                <form action="<?php echo(getSrc('./includes/classes/actions.php')); ?>" method="POST" class="ajax">
                <div class="row">
                    <div class="col-lg-1">
                    </div>

                    <div class="col-lg">

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

            </div>

            <div class="col-lg-2">
            </div>

        </div>



        </section>

    </div>

</div>
