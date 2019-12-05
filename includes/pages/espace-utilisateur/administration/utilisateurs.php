<div class="row">
    <div class="col panel-outline">
        <?php 
        $query = mysqli_query($connection, "SELECT * FROM kioui_accounts");
        $res = "<table class='table'>";
        $res .= "<thead class='thead'>";

        $res .= "<th style='width:20%;'>Nom de l'utilisateur</th>";
        $res .= "<th style='width:auto;'>Adresse e-mail</th>";
        $res .= "<th style='width:12%;'>Niveau d'accès</th>";
        $res .= "<th style='width:12%;'>Statut</th>";
        $res .= "<th style='width:12%;'>Quota</th>";
        $res .= "<th style='width:15%;'>Actions</th>";

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
            //Action
            $res .= "<td>";
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