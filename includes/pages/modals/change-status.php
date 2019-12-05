<div class="modal fade" id="modalChangeStatus" tabindex="-1" role="dialog" aria-labelledby="modalChangeStatus" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">

            <div class="modal-body">

                <div class="container-fluid">
                <div class="row">

                    <div class="col-lg-12"  style="text-align: center;">
                        <h4 class="modal-title">MODIFICATION DU STATUT DE L'UTILISATEUR</h4>
                    </div>

                    <div class="col-lg-2">

                    </div>

                    <div class="col-lg-8">


                       <form action="<?php echo(getSrc('./includes/classes/actions.php')); ?>" method="POST" class="ajax" style="text-align: center;">

                        <span>Veuillez saisir le nouveau statut de l'utilisateur.</span>
                                
                            <input type="hidden" class="form-control" id="change-status_iduser" name="change-status_iduser" value="{ID}" />
                            <br />
                            <br />

                            <select class="form-control" id="change-status_newstatus" name="change-status_newstatus">
                                <option value="ALIVE">OK</option>
                                <option selected value="SUSPENDED">Suspendu</option>
                            </select>

                            <br />
                            <br />
                            <input type="hidden" name="action" value="change-status" />
                            <input type="submit" value="Modifier" name="Modifier" />


                            <button type="button" data-dismiss="modal">Annuler</button>

                            <div style="display: none;" id="hint_change-status"></div>

                        </form>


                    </div>

                    <div class="col-lg-2">

                    </div>
                </div>
                </div>
            </div>
        </div>
    </div>
</div>
