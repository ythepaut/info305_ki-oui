<div class="row">
    <div class="col panel-outline">
        <?php 
        $query = mysqli_query($connection, "SELECT * FROM kioui_files");
        $res = "<table class='table'>";
        $res .= "<thead class='thead'>";

        $res .= "<th style='width:auto'>Nom du fichier</th>";
        $res .= "<th style='width:25%'>Taille</th>";
        $res .= "<th style='width:25%'>Propriétaire</th>";
        $res .= "<th style='width:25%'>Date d'ajout</th>";
        $res .= "<th style='width:auto'>Actions</th>";

        $res .= "</thead>";

        while ($file = mysqli_fetch_assoc($query)) {

        	//$owner = mysql_fetch_object(mysqli_query($connection, "SELECT username FROM kioui_accounts WHERE id = $file["owner"] limit 1"));

            $res .= "<tr>";
            //Affichage du nom du ficher
            $res .= "<td>";
            $res .= $file["original_name"]; // A CHANGER !!!
            $res .= "</td>";
            //Affichage de la taille du fichier
            $res .= "<td>";
            $res .= convertUnits($file["size"]);
            $res .= "</td>";
            //Affichage du propriétaire
            $res .= "<td>";
            $res .= $file["owner"]; // A CHANGER !!!
            $res .= "</td>";
            //Affichage de la date d'upload
            $res .= "<td>";
            $res .= date("d/m/Y", $file["upload_date"]);
            $res .= "</td>";
            //Action
            $res .= "<td>";
            $res .= "<a href='#' title=\"Supprimer le fichier\" data-toggle='modal' data-target='#modalDeleteFile' onclick='editModalDelete(" . $file['id'] . ")'><i class='fas fa-trash-alt delete'></i></a>&nbsp;";
            $res .= "</td>";

            $res .= "</tr>";
        }

        $res .="</table>";
        echo($res);

        include("./includes/pages/modals/delete-file.php");
        ?>
    </div>
</div>