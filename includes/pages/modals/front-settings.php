<div class="modal fade" id="modalFrontSettings" tabindex="-1" role="dialog" aria-labelledby="modalFrontSettings" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">

            <div class="modal-body">

                <div class="container-fluid">

                    <div class="row">

                        <div class="col-lg-12"  style="text-align: center;">
                            <h4 class="modal-title">PARAMÈTRES</h4>
                        </div>

                    </div>

                    <hr/>

                    <div class="row">

                        <div class="col-lg-2">
                            <h5>Langue</h5>
                        </div>

                        <div class="col-lg-2">
                            <button name="language" value="french" checked>Français</button>
                        </div>

                        <div class="col-lg-2">
                            <button name="language" value="english">Anglais</button>
                        </div>

                    </div>

                    <hr/>

                    <div class="row">

                        <div class="col-lg-2">
                            <h5>Thème</h5>
                        </div>

                        <div class="col-lg-2">
                            <button name="theme" value="kioui" onclick="editModalTheme('kioui');">Ki-Oui</button>
                        </div>

                        <div class="col-lg-2">
                            <button name="theme" value="frez" onclick="editModalTheme('frez');">Fr-Ez</button>
                        </div>

                        <div class="col-lg-2">
                            <button name="stheme" value="braille" onclick="editModalTheme('braille');">Braille</button>
                        </div>

                    </div>

                    <hr/>

                    <div class="row">

                        <div class="col-lg-2">
                            <h5>Narrateur</h5>
                        </div>

                        <div class="col-lg-2">
                            <button id="tts" onclick="editModalTTS();">Désactivé</button>
                        </div>

                    </div>


                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo(getSrc('./js/index.js')); ?>"></script>
<script src="<?php echo(getSrc('./js/tts.js')); ?>"></script>