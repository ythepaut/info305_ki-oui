<div class="container-fluid">
    <div class="row">
        <?php include("./includes/pages/espace-utilisateur/nav-utilisateur.php"); ?>

        <section class="col-lg-10 panel-background">
        <div class="row">

            <?php

            if (!isset($_GET['ticket'])) {
                ?>

                <?php
                $query = "SELECT * FROM kioui_tickets WHERE user = " . $_SESSION['Data']['id'] . " ORDER BY `date` DESC";
                $results = mysqli_query($connection, $query);
                $openedTicketCount = 0;
                $table = "";
                $status = "";
                $priority = "";

                //Suppression des fichiers
                while ($ticket = mysqli_fetch_assoc($results)) {
                    if ($ticket['status'] == "OPENED") {
                        $openedTicketCount++;
                    }
                    
                    switch ($ticket['status']) {
                        case "OPEN":
                            $status = "<span class='badge badge-info'>Ouvert</span>";
                            break;
                        case "CLOSED_BY_USER":
                            $status = "<span class='badge badge-danger' title='Vous avez fermé cette demande'>Fermé</span>";
                            break;
                        case "CLOSED_BY_SUPPORT":
                            $status = "<span class='badge badge-danger' title='Le support a fermé cette demande'>Fermé</span>";
                            break;
                        case "EXPIRED":
                            $status = "<span class='badge badge-danger' title=\"Cette demande est expirée car vous n'y avez pas répondu.\">Expirée</span>";
                            break;
                        case "RESPONDED":
                            $status = "<span class='badge badge-success'>Répondu</span>";
                            break;
                        default:
                            $status = "<span class='badge badge-danger'>Erreur</span>";
                            break;
                    }
                    
                    switch ($ticket['priority']) {
                        case "LOW":
                            $priority = "<span class='badge badge-success'>Faible</span>";
                            break;
                        case "MEDIUM":
                            $priority = "<span class='badge badge-warning'>Moyenne</span>";
                            break;
                        case "HIGH":
                            $priority = "<span class='badge badge-danger'>Haute</span>";
                            break;
                        case "HIGHEST":
                            $priority = "<span class='badge badge-danger'>Prioritaire</span>";
                            break;
                        default:
                            $priority = "<span class='badge badge-danger'>Erreur</span>";
                            break;
                    }

                    $subject = (strlen($ticket["subject"]) > 80) ? substr($ticket["subject"], 0, 77) . "..." : $ticket["subject"];

                    //Colonne Sujet
                    $table .=  "<tr><td><a href='/espace-utilisateur/assistance/" . $ticket['id'] . "/' class='link' style='font-weight: bold; color: #212529;'><span title='" . htmlspecialchars($ticket["subject"]) . "'>" . htmlspecialchars($subject) . "</span></a></td>\n";
                    //Colonne Date
                    $table .=  "<td>" . date("d/m/Y", $ticket["date"]) . "&nbsp;&nbsp;&nbsp;" . date("H:i:s", $ticket["date"]) . "</td>\n";
                    //Colonne Priorité
                    $table .=  "<td>" . $priority . "</td>\n";
                    //Colonne Statut
                    $table .=  "<td>" . $status . "</td>\n";
                    //Colonne Action
                    $table .=  "<td>" . "<a href='/espace-utilisateur/assistance/" . $ticket['id'] . "/' title='Visualiser'><i class='fas fa-eye edit'></i></a>" . "</td></tr>\n";

                }
                ?>



                <div class="col panel-outline">
                    <h4 class="panel-title">Mes demandes de support &nbsp;(<?php echo($openedTicketCount); ?>)</h4>


                    <table class="table">
                        <thead class="thead">
                            <th style="width:45%;">Sujet</th>
                            <th style="width:19%;">Date</th>
                            <th style="width:12%;">Priorité</th>
                            <th style="width:12%;">Statut</th>
                            <th style="width:12%;">Actions</th>
                        </thead>

                        <tr><td><a href="#" data-toggle="modal" data-target="#modalCreateTicket"><span class='badge badge-primary'>Créer une demande</span></a></td><td>-</td><td>-</td><td>-</td><td></td></tr>

                        <?php echo($table); ?>

                    </table>

                </div>


                <div class="col-lg-3 panel-outline">
                    <h4 class="panel-title">Incidents en cours</h4>

                    <ul style="list-style-type: none;">
                        <li><i class="fas fa-exclamation-circle warning"></i> &nbsp; &nbsp; Maintenances<br /><b>├</b> &nbsp; &nbsp; &nbsp; Fonctionnalités en cours de developpement<br /><b>└</b> &nbsp; &nbsp; &nbsp; Site en cours de developpement</li>
                        <li><i class="fas fa-check success"></i> &nbsp; &nbsp; Serveur web en ligne</li>
                        <li><i class="fas fa-check success"></i> &nbsp; &nbsp; Serveur de stockage en ligne</li>
                    </ul>
                
                    <h4 class="panel-title">Ressources</h4>

                    <ul style="list-style-type: none;">

                        <li><a href="#" class="link">Comment mes données sont chiffrées ?</a></li>
                        <li><a href="#" class="link">Comment controler mes données ?</a></li>

                    </ul>

                </div>



                <?php
            } else {


                $query = $connection->prepare("SELECT * FROM kioui_tickets WHERE id = ?");
                $query->bind_param("i", $_GET['ticket']);
                $query->execute();
                $result = $query->get_result();
                $query->close();
                $ticket = $result->fetch_assoc();

                if ($ticket['user'] != $_SESSION['Data']['id']) {
                    ?>
                    <div class="col-lg panel-outline">
                        <h4 class="panel-title">Demande inéxistante</h4>
                    </div>
                    <?php
                } else {

                ?>

                <div class="col-lg panel-outline">
                    <h4 class="panel-title">Demande  #<?php echo($ticket['id']); ?> &nbsp; - &nbsp; <?php echo($ticket['subject']); ?></h4>

                    <form action="<?php echo(getSrc('./includes/classes/actions.php')); ?>" method="POST" class="ajax">
                    <table class="table">
                        <thead class="thead">
                            <th style="width:20%;"><!--Expediteur--></th>
                            <th style="width:67%;"><!--Message--></th>
                            <th style="width:13%;"><!--Date--></th>
                        </thead>

                        <?php
                            $table = "";
                            $conversation = json_decode($ticket['conversation'], true);

                            foreach ($conversation as $message) {

                                $sender = $message['senderName'];
                                $sender = ($message['senderRole'] == 'SUPPORT') ? "<span class='badge badge-danger'>Administrateur</span> &nbsp; " . $sender : $sender;

                                $messageStr = htmlspecialchars($message['message']);
                                $messageStr = str_replace("\n", "<br />", $messageStr);

                                $table .= "<tr><td>" . $sender ."</td>\n";
                                $table .= "<td>" . $messageStr ."</td>\n";
                                $table .= "<td>" . date("d/m/Y", $message['date']) . "&nbsp;&nbsp;&nbsp;" . date("H:i:s", $message['date']) ."</td></tr>";
                            }

                            echo($table);


                        ?>
                        
                        <tr>
                            <td><br /><b>Répondre :</b></td>
                            <td>
                                <br />
                                <div class="form-group">
                                    <textarea class="form-control" rows="5" name="respond-ticket_message" required></textarea>
                                </div>
                                <div style="display: none;" id="hint_respond-ticket"></div>
                            </td>
                            <td>
                                <br />
                                <input type="hidden" name="respond-ticket_id" value="<?php echo($ticket['id']); ?>">
                                <input type="hidden" name="action" value="respond-ticket">
                                <input type="submit" value="Répondre" name="Répondre" />
                            </td>
                        </tr>

                    </table>
                    </form>
                    

                </div>



                <?php
                }
            }
            
            ?>

        </div>
        </section>

    </div>

</div>

<?php
include("./includes/pages/modals/create-ticket.php");
?>