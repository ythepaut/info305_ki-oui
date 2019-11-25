<div class="accueil container-fluid">
    <div class="row justify-content-center">
        <div class="col-5 align-self-center box" style="text-align:center;">
            <form action="<?php echo(getSrc('./includes/classes/actions.php')); ?>" method="post" enctype="multipart/form-data" id="uploadForm">
                <div id="allInputs"></div>

                <label for="inputFile" id="inputLabel"></label>

                <table class="table" id="files_tab">
                    <thead class="thead-light">
                        <th scope="col">Nom</th>
                        <th scope="col">Taille</th>
                    </thead>

                    <tr id="first_line_tab">
                        <td scope="row" colspan="2"><i>Aucun fichier sélectionné</i></td>
                    </tr>
                </table>

                <input type="text" name="action" value="upload-file" hidden />

                <button type="button" name="button" onclick="sendFiles();" id="boutonEnvoi">
                    <span id="envoyer" style="display:block;">Envoyer</span>
                    <span id="envoi" style="display:none;">
                        <span class="spinner-border spinner-border-sm" role="status" aria-hidden="true"></span>
                        Envoi...
                    </span>
                </button>

                <script type="text/javascript" src="<?php echo(getSrc('./js/upload.js')); ?>"></script>
                <script>init();</script>
            </form>
        </div>
    </div>
</div>

<!--
    TODO: vérif si upload même fichier ?
-->
