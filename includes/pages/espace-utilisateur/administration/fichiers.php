<div class="row">
    <div class="col panel-outline">

        <br />
        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <span class="input-group-text label-icon" id="icon_search"><i class="fas fa-search"></i></span>
            </div>
            <input type="text" class="form-control search-input" placeholder="Rechercher..." aria-describedby="icon_search" data-target="file_table" />
        </div>
        <br />


        <?php 
        $query = mysqli_query($connection, "SELECT * FROM kioui_files ORDER BY id DESC");
        $res = "<table class='table' id='file_table'>";
        $res .= "<thead class='thead'>";

        $res .= "<th style='width:10%'>Id du fichier</th>";
        $res .= "<th style='width:20%'>Chemin</th>";
        $res .= "<th style='width:20%'>Propriétaire</th>";
        $res .= "<th style='width:20%'>Adresse IP</th>";
        $res .= "<th style='width:20%'>Taille</th>";
        $res .= "<th style='width:20%'>Date d'ajout</th>";
        $res .= "<th style='width:auto'>Actions</th>";

        $res .= "</thead>";

        while ($file = mysqli_fetch_assoc($query)) {

        	//Récupération du propriétaire
            $query2 = $connection->prepare("SELECT username FROM kioui_accounts WHERE id = ?");
            $query2->bind_param("s", $file["owner"]);
            $query2->execute();
            $result = $query2->get_result();
            $query2->close();
            $owner = $result->fetch_assoc();

            $res .= "<tr>";
            //Affichage de l'id du ficher
            $res .= "<td>";
            $res .= $file["id"];
            $res .= "</td>";
            //Affichage du chemmin du fichier
            $res .= "<td>";
            $res .= $file["path"];
            $res .= "</td>";
            //Affichage du propriétaire
            $res .= "<td>";
            $res .= implode( $owner );
            $res .= "</td>";
            //Affichage de l'ip du prorpio
            $res .= "<td>";
            $res .= $file["ip"];
            $res .= "</td>";
            //Affichage de la taille du fichier
            $res .= "<td>";
            $res .= convertUnits($file["size"]);
            $res .= "</td>";
            //Affichage de la date d'upload
            $res .= "<td>";
            $res .= date("d/m/Y", $file["upload_date"]);
            $res .= "</td>";
            //Action
            $res .= "<td style='text-align:center'>";
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