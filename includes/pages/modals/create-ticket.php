
<div class="modal fade" id="modalCreateTicket" tabindex="-1" role="dialog" aria-labelledby="modalCreateTicket" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">

            <div class="modal-body">

                <div class="container-fluid">
                <div class="row">

                    <div class="col-lg-12"  style="text-align: center;">
                        <h4 class="modal-title">CREER UNE DEMANDE D'ASSISTANCE</h4>
                    </div>
                    

                    <div class="col-lg">
                    <form action="<?php echo(getSrc('./includes/classes/actions.php')); ?>" method="POST" class="ajax">
                                                
                        <div class="form-group">
                            <label for="create-ticket_subject">Sujet de votre demande</label>
                            <input type="text" class="form-control" name="create-ticket_subject" id="create-ticket_subject" maxlength="180" required />
                        </div>

                        <br />

                        <div class="form-group">
                            <label for="create-ticket_message">Description de votre demande</label>
                            <textarea class="form-control" rows="5" name="create-ticket_message" id="create-ticket_message" required></textarea>
                        </div>

                        <br />

                        <input type="hidden" name="action" value="create-ticket">

                        <div style="text-align: center;">
                            
                            <input type="submit" value="Valider" />
                            <button data-dismiss="modal">Annuler</button>

                        </div>



                    </form>
                    </div>



                    <div class="col-lg-12">
                        <div style="display: none;" id="hint_create-ticket"></div>
                    </div>

                
                </div>
                </div>

            </div>


        </div>

    </div>
</div>

