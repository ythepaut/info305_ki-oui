
<div class="modal fade" id="modalShareLink" tabindex="-1" role="dialog" aria-labelledby="modalShareLink" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">

            <div class="modal-body">

                <div class="container-fluid">
                <div class="row">

                    <div class="col-lg-12"  style="text-align: center;">
                        <h4 class="modal-title">LIEN DE TÉLÉCHARGEMENT</h4>
                    </div>



                    <div class="col-lg">


                        <span>Cliquez sur le lien pour le copier, puis partagez le avec vos contact.</span>
                        <br />

                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text label-icon" id="icon_login_sharelink"><i class="fas fa-link"></i></span>
                            </div>
                            <input type="text" class="form-control" aria-describedby="icon_login_sharelink" id="input-sharelink" value="<?php echo("{LIEN}"); ?>" readonly onclick="this.select();document.execCommand('copy');"  tabindex="0" data-trigger="focus" data-container="body" data-toggle="popover" data-placement="bottom" data-content="Lien copié !" />
                        </div>

                        <br />



                    </div>




                </div>
                </div>

            </div>


        </div>

    </div>
</div>

<script>
document.addEventListener("DOMContentLoaded", function() {
    $('#input-sharelink').popover();
})
</script>