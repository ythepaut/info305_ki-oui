<?php
if (isset($_GET['sp']) && ($_GET['sp'] == "email" || ($_GET['sp'] == "totp" && $_SESSION['Data']['totp'] != "") || ($_GET['sp'] == "u2f" && $_SESSION['Data']['u2f'] != ""))) {
    $_SESSION['tfa_method'] = $_GET['sp'];
}
?>
<div class="container-fluid">
    <div class="row">
        <?php include("./includes/pages/espace-utilisateur/nav-utilisateur.php"); ?>

        <section class="col-lg panel-background">

        <div class="row">

            <div class="col-lg-2">
            </div>

            <div class="col-lg-8 panel-outline">
                    
                <h4 class="panel-title">VERIFICATION À DOUBLE FACTEUR</h4>

                <?php if ($_SESSION['tfa'] == "new_device" && $_SESSION['tfa_method'] == "totp") { ?>

                    <form action="<?php echo(getSrc('./includes/classes/actions.php')); ?>" method="POST" class="ajax">
                    <div class="row">
                        <div class="col-lg-1">
                        </div>

                        <div class="col-lg">

                            <div style="text-align: center;">
                                <span>Nous ne reconnaissons pas cet appareil.</span><br />
                                <span>Veuillez confirmer votre identité en saisissant votre code d'application TOTP ci-dessous.</span>
                            </div>

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

                <?php } elseif ($_SESSION['tfa'] == "new_device" && $_SESSION['tfa_method'] == "u2f") { ?>

                    <form action="<?php echo(getSrc('./includes/classes/actions.php')); ?>" method="POST" class="ajax" id="validate-u2f">
                    <div class="row">
                        <div class="col-lg-1">
                        </div>

                        <div class="col-lg" style="text-align: center;">

                            <span>Nous ne reconnaissons pas cet appareil.</span><br />
                            <span>Veuillez confirmer votre identité en connectant votre clé de sécurité.</span>

                            <br />
                            <br />
                            <img alt="Inserer clé U2F" src="<?php echo(getSrc('./ressources/img/u2f.png')); ?>" />
                            <br />
                            <br />

                            <?php
                                $scheme = isset($_SERVER['HTTPS']) ? "https://" : "http://";
                                $u2f = new u2flib_server\U2F($scheme . $_SERVER['HTTP_HOST']);
                            ?>
                            <script src="<?php echo(getSrc('./js/u2f-api.js')); ?>"></script>

                            <script>
                                function signU2FKey() {
                                    regbtn = document.querySelector("#sigbtn");
                                    regbtn.setAttribute("disabled", "disabled");
                                    regbtn.innerHTML = " Branchez votre clé ";
                                    <?php                                    
                                    $regs = array(json_decode($_SESSION['Data']['u2f']));
                                    $data = $u2f->getAuthenticateData($regs);

                                    echo "var registrations = " . $_SESSION['Data']['u2f'] . ";\n";
                                    echo "var request = " . json_encode($data) . ";\n";

                                    ?>
                                    
                                    console.log("sign: ", request);
                                    var appId = request[0].appId;
                                    var challenge = request[0].challenge;
                                    console.log("appId: ", appId);
                                    console.log("challenge: ", challenge);
                                    console.log("registeredKeys: ", request);
                                    u2f.sign(appId, challenge, request, function(data) {
                                        var form = document.getElementById('validate-u2f');
                                        var reg = document.getElementById('validate-u2f-reg');
                                        var req = document.getElementById('validate-u2f-req');
                                        var regs = document.getElementById('validate-u2f-rgs');
                                        console.log("Authenticate callback", data);
                                        reg.value=JSON.stringify(data);
                                        req.value=JSON.stringify(request);
                                        regs.value=JSON.stringify(registrations);
                                        form.submit();
                                    });

                                }

                            </script>
                            <input type="hidden" name="validate-u2f_reg" value="" id="validate-u2f-reg" />
                            <input type="hidden" name="validate-u2f_req" value="" id="validate-u2f-req" />
                            <input type="hidden" name="validate-u2f_rgs" value="" id="validate-u2f-rgs" />

                            <input type="hidden" name="action" value="validate-u2f" />

                            <button type="button" id="sigbtn" onclick="signU2FKey();">Utiliser ma clé de sécurité</button>
                        
                        </div>

                        <div class="col-lg-1">
                        </div>
                    </div>

                    </form>
                    <div style="display: none;" id="hint_validate-u2f"></div>

                    <br />

                <?php } elseif ($_SESSION['tfa'] == "new_device" && $_SESSION['tfa_method'] == "email") { ?>

                    <form action="<?php echo(getSrc('./includes/classes/actions.php')); ?>" method="POST" class="ajax">
                    <div class="row">
                        <div class="col-lg-1">
                        </div>

                        <div class="col-lg">

                            <div style="text-align: center;">
                                <span>Nous ne reconnaissons pas cet appareil.</span><br />
                                <span>Veuillez confirmer votre identité en saisissant le code qui vient de vous être envoyé à <b><?php echo($_SESSION['Data']['email']); ?></b>.</span>
                            </div>

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


            <div class="col-lg-12" style="text-align: center;"> &nbsp; &nbsp; &nbsp;
                    <?php if ($_SESSION['tfa_method'] != "email") { ?>
                        <a href="./email" class="link"><i class="fas fa-envelope-open-text"></i> &nbsp; Utiliser mon adresse e-mail</a> &nbsp; &nbsp; &nbsp;
                    <?php } ?>
                    <?php if ($_SESSION['tfa_method'] != "totp" && $_SESSION["Data"]['totp'] != "") { ?>
                        <a href="./totp" class="link"><i class="fas fa-stopwatch"></i> &nbsp; Utiliser mon code d'application</a> &nbsp; &nbsp; &nbsp;
                    <?php } ?>
                    <?php if ($_SESSION['tfa_method'] != "u2f" && $_SESSION["Data"]['u2f'] != "") { ?>
                        <a href="./u2f" class="link"><i class="fas fa-key"></i> &nbsp; Utiliser ma clé de sécurité</a> &nbsp; &nbsp; &nbsp;
                    <?php } ?>
            </div>

        </div>



        </section>

    </div>

</div>
