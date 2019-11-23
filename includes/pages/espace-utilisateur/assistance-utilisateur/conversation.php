
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
                    <h4 class="panel-title">Demande  #<?php echo($ticket['id']); ?> &nbsp; - &nbsp; <?php echo($ticket['subject']); ?></h4>

                    <form action="<?php echo(getSrc('./includes/classes/actions.php')); ?>" method="POST" class="ajax">
                    <table class="table">
                        <thead class="thead">
                            <th style="width:15%;"><!--Expediteur--></th>
                            <th style="width:70%;"><!--Message--></th>
                            <th style="width:15%;"><!--Date--></th>
                        </thead>

                        <?php
                            $table = "";
                            $conversation = json_decode($ticket['conversation'], true);
                            foreach ($conversation as $message) {
                                $sender = htmlspecialchars($message['senderName']);
                                $sender = ($message['senderRole'] == 'SUPPORT') ? "<span class='badge badge-danger'>Administrateur</span> <br /> " . $sender : $sender;
                                $messageStr = htmlspecialchars($message['message']);
                                $messageStr = str_replace("\n", "<br />", $messageStr);
                                $table .= "<tr><td style='text-align: center;'>" . $sender ."</td>\n";
                                $table .= "<td>" . $messageStr ."</td>\n";
                                $table .= "<td title='" . date("d/m/Y", $message['date']) . "&nbsp;&nbsp;&nbsp;" . date("H:i:s", $message['date']) . "'>" . "<span class='badge badge-secondary'>" . time_elapsed_string("@" . $message['date']) . "</span>" ."</td></tr>";
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
                ?>