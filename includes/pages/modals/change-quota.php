<div class="modal fade" id="modalChangeQuota" tabindex="-1" role="dialog" aria-labelledby="modalChangeQuota" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">

            <div class="modal-body">

                <div class="container-fluid">
                <div class="row">

                    <div class="col-lg-12"  style="text-align: center;">
                        <h4 class="modal-title">CHANGEMENT DE QUOTA</h4>
                    </div>

                    <div class="col-lg-2">

                    </div>

                    <div class="col-lg-8">


                       <form action="<?php echo(getSrc('./includes/classes/actions.php')); ?>" method="POST" class="ajax" style="text-align: center;">

                        <span>Veuillez saisir le nouveau quota de l'utilisateur</span>
                                
                            <input type="hidden" class="form-control" id="change-quota_iduser" name="change-quota_iduser" value="{ID}" />
                            <br />
                            <br />

                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text label-icon" id="icon_change-quota_value"><i class="fas fa-tachometer-alt"></i></span>
                                </div>
                                <input type="number" min=0 name="change-quota_value" class="form-control" placeholder="Nouveau quota" aria-describedby="icon_change-quota_value" required />
                            </div>
                            Unitée
                            <select id="change-quota_units" name="change-quota_units">
                                <option value="o">Octets</option>
                                <option value="Ko">Kilooctet</option>
                                <option selected value="Mo">Mégaoctet</option>
                                <option value="Go">Gigaoctet </option>
                                <option value="To">Téraoctet </option>
                            </select>

                            <br />
                            <br />
                            <input type="hidden" name="action" value="change-quota" />
                            <input type="submit" value="Modifier" name="Modifier" />


                            <button type="button" data-dismiss="modal">Annuler</button>

                            <div style="display: none;" id="hint_change-quota"></div>

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
