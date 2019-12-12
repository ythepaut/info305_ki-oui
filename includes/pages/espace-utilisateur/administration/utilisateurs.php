<div class="row">
    <div class="col panel-outline">

        <br />
        <div class="input-group mb-3">
            <div class="input-group-prepend">
                <span class="input-group-text label-icon" id="icon_search"><i class="fas fa-search"></i></span>
            </div>
            <input type="text" class="form-control search-input" placeholder="Rechercher..." aria-describedby="icon_search" data-target="user_table" />
        </div>
        <br />

        <?php 
        $query = mysqli_query($connection, "SELECT * FROM kioui_accounts");
        $res = "<table class='table' id='user_table'>";
        $res .= "<thead class='thead'>";

        $res .= "<th style='width:15%;'>Nom de l'utilisateur</th>";
        $res .= "<th style='width:auto;'>Adresse e-mail</th>";
        $res .= "<th style='width:13%;'>Niveau d'accès</th>";
        $res .= "<th style='width:10%;'>Statut</th>";
        $res .= "<th style='width:8%;'>Quota</th>";
        $res .= "<th style='width:19%;'>Utilisation</th>";
        $res .= "<th style='width:9%; text-align: right;'>Actions</th>";

        $res .= "</thead>";

        while ($user = mysqli_fetch_assoc($query)) {

            $accesslevel = "";

            switch ($user["access_level"]) {
                case "ADMINISTRATOR":
                    $accesslevel = "<span class='badge badge-warning'>Administrateur</span>";
                    break;
                case "USER":
                    $accesslevel = "<span class='badge badge-primary'>Utilisateur</span>";
                    break;
                case "GUEST":
                    $accesslevel = "<span class='badge badge-secondary'>Invité</span>";
                    break;
            }

            $status = "";

            switch ($user["status"]) {
                case "SUSPENDED":
                    $status = "<span class='badge badge-danger'>Suspendu</span>";
                    break;
                case "DELETE_PROCEDURE":
                    $status = "<span class='badge badge-warning'>Suppression</span>";
                    break;
                case "REGISTRATION":
                    $status = "<span class='badge badge-info'>Enregistrement</span>";
                    break;
                case "ALIVE":
                    $status = "<span class='badge badge-primary'>OK</span>";
                    break;
            }


            $pourcentage = round((getSize($user['id'], $connection) / $user['quota']) * 100, 2);
            $pourcentage = ($pourcentage == 0) ? "0.00" : $pourcentage;
            $occupe = round(getSize($user['id'], $connection), 2);

            $couleur = "bg-success";

            if ($pourcentage > 80) {
                $couleur = "bg-danger";
            } elseif ($pourcentage > 60) {
                $couleur = "bg-warning";
            }

            $res .= "<tr>";
            //Affichage du nom de l'utilisateur
            $res .= "<td>";
            $res .= htmlspecialchars($user["username"]);
            $res .= "</td>";
            //Affichage de l'email
            $res .= "<td>";
            $res .= htmlspecialchars($user["email"]);
            $res .= "</td>";
            //Affichage du niveau d'accès
            $res .= "<td>";
            $res .= $accesslevel;
            $res .= "</td>";
            //Affichage du statut
            $res .= "<td>";
            $res .= $status;
            $res .= "</td>";
            //Affichage du quota
            $res .= "<td>";
            $res .= convertUnits($user["quota"]);
            $res .= "</td>";
            //Affichage utilisation espace
            $res .= "<td>";
            $res .= "<div class='progress' title='" . convertUnits($occupe) . "/" . convertUnits($user['quota']) . "  (" . $pourcentage . "%)" . "'> <div class='progress-bar " . $couleur . "' role='progressbar' style='width: " . $pourcentage . "%' aria-valuenow='" . $pourcentage . "' aria-valuemin='0' aria-valuemax='100'></div> </div>";
            $res .= "</td>";
            //Action
            $res .= "<td style='text-align: right;'>";
            $res .= "<a href='#' title=\"Modifier le quota\" data-toggle='modal' data-target='#modalChangeQuota' onclick='editModalQuota(" . $user['id'] . ")'><i class='fas fa-tachometer-alt edit'></i></a>&nbsp;&nbsp;&nbsp;";
            $res .= "<a href='#' title=\"Modifier le niveau d'access\" data-toggle='modal' data-target='#modalChangeAccessLevel' onclick='editModalAccessLevel(" . $user['id'] . ")'><i class='far fa-id-card edit'></i></i></a>&nbsp;&nbsp;&nbsp;";
            $res .= "<a href='#' title=\"Modifier le statut du compte\" data-toggle='modal' data-target='#modalChangeStatus' onclick='editModalStatus(" . $user['id'] . ")'><i class='fas fa-gavel edit'></i></i></a>";
            $res .= "</td>";

            $res .= "</tr>";
        }

        $res .="</table>";
        echo($res);
        include("./includes/pages/modals/change-access-level.php");
        include("./includes/pages/modals/change-quota.php");
        include("./includes/pages/modals/change-status.php");
        ?>
    </div>
</div>