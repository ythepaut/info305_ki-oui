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
                            <input type="radio" name="language" value="french" checked> Français
                        </div>

                        <div class="col-lg-2">
                            <input type="radio" name="language" value="english"> Anglais
                        </div>

                    </div>

                    <hr/>

                    <div class="row">

                        <div class="col-lg-2">
                            <h5>Thème</h5>
                        </div>

                        <div class="col-lg-2">
                            <input type="radio" name="theme" value="kioui" onclick="editModalTheme('kioui');" />Ki-Oui
                        </div>

                        <div class="col-lg-2">
                            <input type="radio" name="theme" value="frez" onclick="editModalTheme('frez');" />Fr-Ez
                        </div>

                        <div class="col-lg-2">
                            <input type="radio" name="theme" value="dark" onclick="editModalTheme('dark');" />Dark
                        </div>

                    </div>


                </div>
            </div>
        </div>
    </div>
</div>

<script src="<?php echo(getSrc('./js/index.js')); ?>"></script>