
<div class="modal fade" id="modalChangeUsername" tabindex="-1" role="dialog" aria-labelledby="modalChangeUsername" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">

            <div class="modal-body">

                <div class="container-fluid">
                <div class="row">

                    <div class="col-lg-12"  style="text-align: center;">
                        <h4 class="modal-title">CHANGEMENT DE NOM D'UTILISATEUR</h4>
                    </div>
                    
                    <div class="col-lg-2">

                    </div>

                    <div class="col-lg-8">

                        <form action="<?php echo(getSrc('./includes/classes/actions.php')); ?>" method="POST" class="ajax">
                        
                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text label-icon" id="icon_change-username_newusername"><i class="fas fa-user"></i></span>
                                </div>
                                <input type="text" name="change-username_newusername" class="form-control" placeholder="Nouveau nom d'utilisateur" aria-describedby="icon_change-username_newusername" required />
                            </div>

                            <input type="hidden" name="action" value="change-username" />
                            <div style="text-align: center;">
                                <input type="submit" value="Valider" />
                            </div>

                        </form>


                    </div>

                    <div class="col-lg-2">

                    </div>


                    <div class="col-lg-12">

                        <div style="display: none;" id="hint_change-username"></div>
                        
                    </div>


                
                </div>
                </div>

            </div>


        </div>

    </div>
</div>

