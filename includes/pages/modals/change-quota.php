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

                        <span>Êtes-vous sûr de vouloir changer le quota de cet utilisateur ?</span>
                                
                            <input type="hidden" class="form-control" id="change-quota_iduser" name="change-quota_iduser" value="{ID}" />
                            <br />

                            <div class="input-group mb-3">
                                <div class="input-group-prepend">
                                    <span class="input-group-text label-icon" id="icon_change-quota_value"><i class="fas fa-file"></i></span>
                                </div>
                                <input type="number" min=0 name="change-quota_value" class="form-control" placeholder="Nouveau quota" aria-describedby="icon_change-quota_value" required />
                            </div>
                            Unitée
                            <select id="change-quota_units" name="change-quota_units">
                                <option value="o">octets</option>
                                <option value="Ko">kilooctets</option>
                                <option selected value="Mo">mégaoctets </option>
                                <option value="Go">gigaoctets </option>
                                <option value="To">téraoctets </option>
                                <option value="Po">pétaoctets </option>
                                <option value="Eo">exaaoctets </option>
                                <option value="Zo">zettaoctets </option>
                                <option value="Yo">yottaoctets </option>
                            </select>

                            <br />
                            <input type="hidden" name="action" value="change-quota" />
                            <input type="submit" value="Actualisation" name="Actualisation" />


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
