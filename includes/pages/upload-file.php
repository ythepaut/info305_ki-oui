<div class="accueil container-fluid">
    <div class="row justify-content-center">
        <div class="col-5 align-self-center box" style="text-align:center;">
            <form action="<?php echo(getSrc('./includes/classes/actions.php')); ?>" method="post" enctype="multipart/form-data">
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

                <input type="submit" value="Upload" />

                <script type="text/javascript" src="<?php echo(getSrc('./js/upload.js')); ?>"></script>
                <script>init();</script>
            </form>
        </div>
    </div>
</div>

<!--
    Chaque input de fichier ne peut être set qu'une seule fois, il nous faut donc recréer un input
    à chaque nouvelle sélection. Les input ont le même nom afin d'être regroupés dans la même
    variable tableau dans la partie PHP.
-->

<!--
    TODO: vérif si upload même fichier ?
-->
