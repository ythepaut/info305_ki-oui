
<div class="modal fade" id="modalDlData" tabindex="-1" role="dialog" aria-labelledby="modalDlData" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">

            <div class="modal-body">

                <div class="container-fluid">
                <div class="row">

                    <div class="col-lg-12"  style="text-align: center;">
                        <h4 class="modal-title">TÉLÉCHARGEMENT DE MES DONNÉES</h4>
                    </div>
                    
                    <div class="col-lg-2">

                    </div>

                    <div class="col-lg-8">
                    <form action="<?php echo(getSrc('./includes/classes/actions.php')); ?>" method="POST">
                        
                        <span>Je souhaite télécharger :</span>
                        <br />

                        <ul style="list-style-type: none; margin: 5px 0px;">
                            <li>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="download-data_checked" value="data" id="download-data_db" checked disabled />
                                    <label class="custom-control-label" for="download-data_db">Toutes les données de mon compte (Nom d'utilisateur, mot de passe crypté, email, etc...).</label>
                                </div>
                            </li>
                            <li>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="download-data_checked" value="encfiles" id="download-data_encrypted-files" disabled />
                                    <label class="custom-control-label" for="download-data_encrypted-files">Mes fichiers tels qu'ils sont stockés sur le serveur de KI-OUI.</label>
                                </div>
                            </li>
                            <li>
                                <div class="custom-control custom-checkbox">
                                    <input type="checkbox" class="custom-control-input" name="download-data_checked" value="decfiles" id="download-data_decrypted-files" disabled />
                                    <label class="custom-control-label" for="download-data_decrypted-files">Mes fichiers décryptés.</label>
                                </div>
                            </li>
                        </ul>


                        <br />

                        <div class="input-group mb-3">
                            <div class="input-group-prepend">
                                <span class="input-group-text label-icon" id="icon_download-data_passwd"><i class="fas fa-unlock"></i></span>
                            </div>
                            <input type="password" name="download-data_passwd" class="form-control" placeholder="Mot de passe requis pour confirmer votre action" aria-describedby="icon_download-data_passwd" required />
                        </div>

                        <br />
                        

                        <input type="hidden" name="action" value="download-data">

                        <div style="text-align: center;">
                            
                            <input type="submit" value="Valider">

                        </div>



                    </form>
                    </div>

                    <div class="col-lg-2">

                    </div>


                    <div class="col-lg-12">
                        <div style="display: none;" id="hint_download-data"></div>
                    </div>

                
                </div>
                </div>

            </div>


        </div>

    </div>
</div>

