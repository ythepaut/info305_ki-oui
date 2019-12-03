<div class="modal fade" id="modalChangeAccessLevel" tabindex="-1" role="dialog" aria-labelledby="modalChangeAccessLevel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">

            <div class="modal-body">

                <div class="container-fluid">
                <div class="row">

                    <div class="col-lg-12"  style="text-align: center;">
                        <h4 class="modal-title">CHANGEMENT DE NIVEAU D'ACCES</h4>
                    </div>

                    <div class="col-lg-2">

                    </div>

                    <div class="col-lg-8">


                       <form action="<?php echo(getSrc('./includes/classes/actions.php')); ?>" method="POST" class="ajax" style="text-align: center;">

                        <span>Êtes-vous sûr de vouloir changer le niveau d'accès de cet utilisateur ?</span>
                                
                            <input type="hidden" class="form-control" id="change-access-level_iduser" name="change-access-level_iduser" value="{ID}" />
                            <br />

                            <select id="change-access-level_newstatus" name="change-access-level_newstatus">
                                <option value="GUEST">Invité</option>
                                <option selected value="USER">Utilisateur</option>
                                <option value="ADMINISTRATOR">Administrateur</option>
                            </select>

                            <br />
                            <input type="hidden" name="action" value="change-access-level" />
                            <input type="submit" value="Actualisation" name="Actualisation" />


                            <button type="button" data-dismiss="modal">Annuler</button>

                            <div style="display: none;" id="hint_change-access-level"></div>

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
