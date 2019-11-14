
<div class="modal fade" id="modalTOTP" tabindex="-1" role="dialog" aria-labelledby="modalTOTP" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">

            <div class="modal-body">

                <div class="container-fluid">
                <div class="row">

                    <div class="col-lg-12"  style="text-align: center;">
                        <h4 class="modal-title">DOUBLE AUTHENTIFICATION PAR APPLICATION</h4>
                    </div>
                    
                    <div class="col-lg-2">

                    </div>

                    <div class="col-lg-8">

                        <?php if ($_SESSION['Data']['totp'] == "") { //Activation TOTP ?>

                            <form action="<?php echo(getSrc('./includes/classes/actions.php')); ?>" method="POST" class="ajax">
                            
                                <span>Scannez le QR code ci-dessous, ou entrez manuellement le code sur votre application <a href="#" class="link">Authy</a> ou <a href="#" class="link">Google Authenticator</a>.</span>
                                <br />
                                <br />

                                <div style="text-align: center;">
                                    <?php
                                    $qrCodeUrl = $ga->getQRCodeGoogleUrl('KI-OUI' . ' (' . $_SESSION['Data']['email'] . ')', $_SESSION['totp']);
                                    ?>
                                    <img src="<?php echo $qrCodeUrl; ?>" alt="qrCodeTOTP" />
                                    <br />
                                    <span><em><?php echo($_SESSION['totp']); ?></em></span>
                                </div>
                                
                                <br />
                                <br />
                                <span>Veuillez valider en saisissant le code généré par l'application.</span>
                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text label-icon" id="icon_totp_code"><i class="fas fa-key"></i></span>
                                    </div>
                                    <input type="text" class="form-control" aria-describedby="icon_totp_code" name="enable-totp_code" placeholder="Code TOTP" maxlength="6" required />
                                </div>
                                
                                <br />
                                
                                <input type="hidden" name="enable-totp_key" value="<?php echo($_SESSION['totp']); ?>" />
                                <input type="hidden" name="action" value="enable-totp" />

                                <div style="text-align: center;">
                                    <input type="submit" value="Valider" />
                                </div>

                            </form>
                        
                        <?php } else { //Desactivation TOTP ?>

                            <form action="<?php echo(getSrc('./includes/classes/actions.php')); ?>" method="POST" class="ajax">
                            
                                <p>Vous êtes sur le point de retirer la double authentification par application.<br />
                                Afin de confirmer votre action, veuillez saisir le code totp affiché sur votre application.</p>

                                <br />

                                <div class="input-group mb-3">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text label-icon" id="icon_totp_code"><i class="fas fa-key"></i></span>
                                    </div>
                                    <input type="text" class="form-control" aria-describedby="icon_totp_code" name="enable-totp_code" placeholder="Code TOTP" maxlength="6" required />
                                </div>
                                
                                <br />
                                
                                <input type="hidden" name="action" value="disable-totp" />

                                <div style="text-align: center;">
                                    <input type="submit" value="Valider" />
                                </div>

                            </form>

                        <?php } ?>

                    </div>

                    <div class="col-lg-2">

                    </div>


                    <div class="col-lg-12">

                        <div style="display: none;" id="<?php if ($_SESSION['Data']['totp'] == "") { echo("hint_enable-totp"); } else { echo("hint_disable-totp"); } ?>"></div>
                        
                    </div>


                
                </div>
                </div>

            </div>


        </div>

    </div>
</div>

