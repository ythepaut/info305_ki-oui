<div class="accueil container-fluid">
    <div class="row justify-content-center">
        <div class="col-5 align-self-center box" style="text-align:center;">
            <br>

            <input type="file" name="file" id="inputFile" multiple />
            <label for="inputFile"></label>

            <br>
            <br>
            <br>

            <table id="files_tab">
                <tr>
                    <th>Nom</th>
                    <th>Taille</th>
                </tr>

                <tr id="first_line_tab">
                    <td colspan="2"><i>Aucun fichier sélectionné</i></td>
                </tr>
            </table>

            <br>

            <button>Upload</button>

            <script type="text/javascript" src="./js/upload.js"></script>
        </div>
    </div>
</div>

<!--
    TODO:
    upload
    et si upload même fichier ?
-->
