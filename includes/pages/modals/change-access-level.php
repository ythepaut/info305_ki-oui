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

                        <span>Veuillez saisir le nouveau niveau d'accès de l'utilisateur.</span>
                                
                            <input type="hidden" class="form-control" id="change-access-level_iduser" name="change-access-level_iduser" value="{ID}" />
                            <br />
                            <br />

                            <select id="change-access-level_newstatus" name="change-access-level_newstatus">
                                <option value="GUEST">Invité</option>
                                <option selected value="USER">Utilisateur</option>
                                <option value="ADMINISTRATOR">Administrateur</option>
                            </select>

                            <br />
                            <br />
                            <input type="hidden" name="action" value="change-access-level" />
                            <input type="submit" value="Modifier" name="Modifier" />


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
