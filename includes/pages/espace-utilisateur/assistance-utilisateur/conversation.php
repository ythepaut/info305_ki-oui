
                <?php
                $query = $connection->prepare("SELECT * FROM kioui_tickets WHERE id = ?");
                $query->bind_param("i", $_GET['ticket']);
                $query->execute();
                $result = $query->get_result();
                $query->close();
                $ticket = $result->fetch_assoc();
                if ($ticket['user'] != $_SESSION['Data']['id'] && $_SESSION['Data']['access_level'] != "ADMINISTRATOR") {
                    ?>
                    <div class="col-lg panel-outline">
                        <h4 class="panel-title">Demande inéxistante</h4>
                    </div>
                    <?php
                } else {
                ?>

                <div class="col-lg panel-outline">
                    <h4 class="panel-title"><?php echo(htmlspecialchars($ticket['subject'])); ?><?php if ($_SESSION['Data']['access_level'] == "ADMINISTRATOR" && ($ticket['status'] == "OPEN" || $ticket['status'] == "RESPONDED")) { ?>&nbsp;&nbsp;&nbsp;&nbsp;
                        <a href="/includes/classes/actions.php?action=close-ticket&close-ticket_id=<?php echo($ticket['id']); ?>" style="font-size: 12px;" class="badge badge-primary">Clôturer la demande</a> &nbsp;
                        <a href="/includes/classes/actions.php?action=prior-ticket&close-ticket_id=<?php echo($ticket['id']); ?>&close-ticket_prior=LOW" style="font-size: 12px;" class="badge badge-success"><?php if ($ticket['priority'] == "LOW") { echo("> "); } ?>Définir comme Faible<?php if ($ticket['priority'] == "LOW") { echo(" <"); } ?></a> &nbsp;
                        <a href="/includes/classes/actions.php?action=prior-ticket&close-ticket_id=<?php echo($ticket['id']); ?>&close-ticket_prior=MEDIUM" style="font-size: 12px;" class="badge badge-warning"><?php if ($ticket['priority'] == "MEDIUM") { echo("> "); } ?>Définir comme Moyen<?php if ($ticket['priority'] == "MEDIUM") { echo(" <"); } ?></a> &nbsp;
                        <a href="/includes/classes/actions.php?action=prior-ticket&close-ticket_id=<?php echo($ticket['id']); ?>&close-ticket_prior=HIGH" style="font-size: 12px;" class="badge badge-danger"><?php if ($ticket['priority'] == "HIGH") { echo("> "); } ?>Définir comme Haut<?php if ($ticket['priority'] == "HIGH") { echo(" <"); } ?></a> &nbsp;
                        <a href="/includes/classes/actions.php?action=prior-ticket&close-ticket_id=<?php echo($ticket['id']); ?>&close-ticket_prior=HIGHEST" style="font-size: 12px;" class="badge badge-danger"><?php if ($ticket['priority'] == "HIGHEST") { echo("> "); } ?>Définir comme Urgent<?php if ($ticket['priority'] == "HIGHEST") { echo(" <"); } ?></a> <?php } elseif ($ticket['status'] == "OPEN" || $ticket['status'] == "RESPONDED") { ?>&nbsp;&nbsp;&nbsp;&nbsp;
                        <a href="/includes/classes/actions.php?action=close-ticket&close-ticket_id=<?php echo($ticket['id']); ?>" style="font-size: 12px;" class="badge badge-primary">Fermer ma demande</a>
                        <?php } ?>
                    </h4>

                    <form action="<?php echo(getSrc('./includes/classes/actions.php')); ?>" method="POST" class="ajax">
                    <table class="table">
                        <?php
                            $table = "";
                            $conversation = json_decode($ticket['conversation'], true);
                            foreach ($conversation as $message) {
                                $sender = htmlspecialchars($message['senderName']);
                                $sender = ($message['senderRole'] == 'SUPPORT') ? "<span class='badge badge-info'>Support</span> &nbsp;" . $sender : $sender;
                                $messageStr = htmlspecialchars($message['message']);
                                $messageStr = str_replace("\n", "<br />", $messageStr);
                                $table .= "<tr><td style='width:15%; text-align: center;'>" . $sender ."</td>\n";
                                $table .= "<td style='width:70%;'>" . $messageStr ."</td>\n";
                                $table .= "<td style='width:15%;' title='" . date("d/m/Y", $message['date']) . "&nbsp;&nbsp;&nbsp;" . date("H:i:s", $message['date']) . "'>" . "<span class='badge badge-secondary'>" . time_elapsed_string("@" . $message['date']) . "</span>" ."</td></tr>";
                            }
                            echo($table);
                        ?>
                        
                        <tr>
                            <td><br /><b>Répondre :</b></td>
                            <td>
                                <br />
                                <div class="form-group">
                                    <textarea class="form-control" rows="5" name="respond-ticket_message" required <?php if ($ticket['status'] == 'CLOSED_BY_USER' || $ticket['status'] == 'CLOSED_BY_SUPPORT' || $ticket['status'] == 'EXPIRED') { echo("disabled"); } ?>></textarea>
                                </div>
                                <div style="display: none;" id="hint_respond-ticket"></div>
                            </td>
                            <td>
                                <br />
                                <input type="hidden" name="respond-ticket_id" value="<?php echo($ticket['id']); ?>">
                                <input type="hidden" name="action" value="respond-ticket">
                                <input type="submit" value="Répondre" name="Répondre" <?php if ($ticket['status'] == 'CLOSED_BY_USER' || $ticket['status'] == 'CLOSED_BY_SUPPORT' || $ticket['status'] == 'EXPIRED') { echo("disabled"); } ?> />
                            </td>
                        </tr>

                    </table>
                    </form>
                    

                </div>



                <?php
                }
                ?>