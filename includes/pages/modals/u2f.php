
<div class="modal fade" id="modalU2F" tabindex="-1" role="dialog" aria-labelledby="modalU2F" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">

            <div class="modal-body">

                <div class="container-fluid">
                <div class="row">

                    <div class="col-lg-12"  style="text-align: center;">
                        <h4 class="modal-title">DOUBLE AUTHENTIFICATION PAR CLÉ PHYSIQUE</h4>
                    </div>
                    
                    <div class="col-lg-2">

                    </div>

                    <div class="col-lg-8">

                        <?php if ($_SESSION['Data']['u2f'] == "") { //Activation u2f ?>

                            <form action="<?php echo(getSrc('./includes/classes/actions.php')); ?>" method="POST" class="ajax" id="enable-u2f">

                                <p>Pour enregistrer votre clé, appuyez sur "Enregistrer", branchez votre clé U2F à votre ordinateur, et si besoin, appuyez sur le bouton de votre clé de sécurité.</p>
                                
                                <br />
                                <div style="text-align: center;">
                                    <img alt="Inserer clé U2F" src="<?php echo(getSrc('./ressources/img/u2f.png')); ?>" />
                                <div>
                                <br />

                                <?php
                                    $scheme = isset($_SERVER['HTTPS']) ? "https://" : "http://";
                                    $u2f = new u2flib_server\U2F($scheme . $_SERVER['HTTP_HOST']);
                                ?>
                                <script src="<?php echo(getSrc('./js/u2f-api.js')); ?>"></script>

                                <script>
                                    function registerU2FKey() {
                                        regbtn = document.querySelector("#regbtn");
                                        regbtn.setAttribute("disabled", "disabled");
                                        regbtn.innerHTML = " Branchez votre clé ";
                                        <?php
                                        $regs = array();
                                        list($data, $reqs) = $u2f->getRegisterData($regs);
                                        echo "var request = " . json_encode($data) . ";\n";
                                        echo "var signs = " . json_encode($reqs) . ";\n";
                                        ?>
                                        var appId = request.appId;
                                        var registerRequests = [{version: request.version, challenge: request.challenge, attestation: 'direct'}];
                                        console.log("Register: ", request);
                                        u2f.register(appId, registerRequests, signs, function(data) {
                                            var form = document.getElementById('enable-u2f');
                                            var reg = document.getElementById('enable-u2f-reg');
                                            var req = document.getElementById('enable-u2f-req');
                                            console.log("Register callback", data);
                                            if(data.errorCode && data.errorCode != 0) {
                                                alert("Une erreur est survenue lors de l'enregistrement. (Code erreur : " + data.errorCode + ")");
                                                regbtn.removeAttribute("disabled");
                                                regbtn.innerHTML = "Enregistrer";
                                            } else {
                                                reg.value=JSON.stringify(data);
                                                req.value=JSON.stringify(request);
                                                form.submit();
                                            }
                                        });
                                    }

                                </script>

                                
                                <input type="hidden" name="enable-u2f_reg" value="" id="enable-u2f-reg" />
                                <input type="hidden" name="enable-u2f_req" value="" id="enable-u2f-req" />

                                <input type="hidden" name="action" value="enable-u2f" />

                                <div style="text-align: center;">
                                    <button type="button" onclick="registerU2FKey();" id="regbtn">Enregistrer</button>
                                </div>

                            </form>
                        
                        <?php } else { //Desactivation u2f ?>

                            <form action="<?php echo(getSrc('./includes/classes/actions.php')); ?>" method="POST" class="ajax">
                            
                                <p>Vous êtes sur le point de retirer la double authentification par clé physique U2F.<br />
                                Afin de confirmer votre action, veuillez saisir votre mot de passe.</p>

                                <br />


                                <input type="hidden" name="action" value="disable-u2f" />

                                <div style="text-align: center;">
                                    <input type="submit" value="Valider" />
                                </div>

                            </form>

                        <?php } ?>

                    </div>

                    <div class="col-lg-2">

                    </div>


                    <div class="col-lg-12">

                        <div style="display: none;" id="<?php if ($_SESSION['Data']['u2f'] == "") { echo("hint_enable-u2f"); } else { echo("hint_disable-u2f"); } ?>"></div>
                        
                    </div>


                
                </div>
                </div>

            </div>


        </div>

    </div>
</div>

