<style>
    <?php include 'css/ajout.css'; ?>
</style>

<div class="accueil container-fluid">
    <div class="row justify-content-center">
        <div class="col-5 align-self-center box" style="text-align:center;">
            <form action="./includes/classes/actions.php" method="post" enctype="multipart/form-data">
                <input type="file" name="files[]" id="inputFile" multiple="multiple" />
                <label for="inputFile"></label>

                <table id="files_tab">
                    <tr>
                        <th>Nom</th>
                        <th>Taille</th>
                    </tr>

                    <tr id="first_line_tab">
                        <td colspan="2"><i>Aucun fichier sélectionné</i></td>
                    </tr>
                </table>

                <input type="text" name="action" value="upload" hidden />

                <input type="submit" value="Upload" />

                <script type="text/javascript" src="./js/upload.js"></script>
                <script>init();</script>
            </form>
        </div>
    </div>
</div>

<!--
    TODO:
    upload
    vérif si upload même fichier ?

    BUG:
    dans le JS la fonction est exécutée que lors d'un changement (donc un seul upload de fichiers)
    dans l'upload seul le 1er upload compte

    HACK:
    plein de boutons et de label hidden à chaque nouvel upload ? value marche pas donc pas trop le choix


-->
