<div class="row" style="margin: 0px !important;">
    <div class="col-lg-2">
    </div>
    <div class="col-lg-8">
        <br />

        <h2>Nous contacter</h2>

        <br />


        <form action="<?php echo(getSrc('./includes/classes/actions.php')); ?>" method="POST" class="ajax">
                            

            <div class="form-group">
                <label for="contact-email">Votre adresse e-mail</label>
                <input type="email" class="form-control" name="contact-email" id="contact-email" required />
            </div>

            <br />

            <div class="form-group">
                <label for="contact-subject">Sujet de votre message</label>
                <input type="text" class="form-control" name="contact-subject" id="contact-subject" required />
            </div>

            <br />

            <div class="form-group">
                <label for="contact-message">Votre message</label>
                <textarea class="form-control"rows="10" name="contact-message" id="contact-message" required></textarea>
            </div>

            <br />
                

            <input type="hidden" name="action" value="contact" />

            <input type="submit" value="Envoyer" />

            <br />
            <br />

            <div style="display: none;" id="hint_contact"></div>
            <br />

        </form>



    </div>
    <div class="col-lg-2">
    </div>
</div>