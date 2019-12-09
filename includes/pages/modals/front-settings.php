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
                            <a href="#" name="language" value="french" checked>Français</a>
                        </div>

                        <div class="col-lg-2">
                            <a href="#" name="language" value="english">Anglais</a>
                        </div>

                    </div>

                    <hr/>

                    <div class="row">

                        <div class="col-lg-2">
                            <h5>Thème</h5>
                        </div>

                        <div class="col-lg-2">
                            <a href="#" name="theme" value="kioui" onclick="editModalTheme('kioui');">Ki-Oui</a>
                        </div>

                        <div class="col-lg-2">
                            <a href="#" name="theme" value="frez" onclick="editModalTheme('frez');">Fr-Ez</a>
                        </div>

                        <div class="col-lg-2">
                            <a href="#" name="theme" value="dark" onclick="editModalTheme('dark');">Dark</a>
                        </div>

                        <div class="col-lg-2">
                            <a href="#" name="stheme" value="braille" onclick="editModalTheme('braille');">Braille</a>
                        </div>

                    </div>


                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo(getSrc('./js/index.js')); ?>"></script>