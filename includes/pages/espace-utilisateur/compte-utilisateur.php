<div class="container-fluid">
    <div class="row">
        <?php include("./includes/pages/espace-utilisateur/nav-utilisateur.php"); ?>

        
        <section class="col-lg-10 panel-background">
        <div class="row">

            <div class="col panel-outline">
                <h4 class="panel-title">Mon compte</h4>

                    
            
                <table class="table">
                    <thead class="thead-light">
                        <tr>
                            <th scope="col" style="width: 45%;">INFORMATIONS</th>
                            <th scope="col" style="width: 45%;"></th>
                            <th scope="col" style="width: auto;"></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <th scope="row"> Adresse e-mail</th>
                            <td><?php echo(htmlspecialchars($_SESSION['Data']['email'])); ?></td>
                            <td><a href="#" data-toggle="modal" data-target="#modalChangeEmail"><i class="fas fa-pen edit" title="Modifier"></i></a></td>
                        </tr>
                        <tr>
                            <th scope="row"> Nom d'utilisateur</th>
                            <td><?php echo(htmlspecialchars($_SESSION['Data']['username'])); ?></td>
                            <td><a href="#" data-toggle="modal" data-target="#modalChangeUsername"><i class="fas fa-pen edit" title="Modifier"></i></a></td>
                        </tr>
                    <thead class="thead-light">
                        <tr>
                            <th scope="col" style="width: 45%;">SÉCURITÉ</th>
                            <th scope="col" style="width: 45%;"></th>
                            <th scope="col" style="width: auto;"></th>
                        </tr>
                    </thead>
                        <tr>
                            <th scope="row"> Mot de passe</th>
                            <td>****************</td>
                            <td><a href="#" data-toggle="modal" data-target="#modalChangePassword"><i class="fas fa-pen edit"></i></a></td>
                        </tr>
                        <tr>
                            <th scope="row"> Double authentification par application</th>
                            <td><?php if ($_SESSION['Data']['totp'] == "") { ?>Désactivée &nbsp; <i class="fas fa-exclamation-triangle warning" title="Conseillé d'activer"></i> <?php } else { ?>Activée<?php } ?></td>
                            <td><a href="#" data-toggle="modal" data-target="#modalTOTP"><?php if ($_SESSION['Data']['totp'] != "") { echo("<i class='fas fa-toggle-on enabled' title='Désactiver la double authentification'></i>"); } else { echo("<i class='fas fa-toggle-off disabled' title='Activer la double authentification'></i>"); } ?></a></td>
                        </tr>
                        <tr>
                            <th scope="row"> Clé de secours</th>
                            <td><?php if ($_SESSION['Data']['backup_password'] != "") { echo("Créée"); } else { echo("Non générée &nbsp; <i class='fas fa-exclamation-triangle error' title='Fortement conseillé de recuperer'></i>"); } ?></td>
                            <td><a href="#" data-toggle="modal" data-target="#modalBackupKey"><?php if ($_SESSION['Data']['backup_password'] != "") { echo("<i class='fas fa-sync edit' title='Créer une nouvelle clé de secours'></i>"); } else { echo("<i class='fas fa-plus-square edit' title='Créer une clé de secours'></i>"); } ?></a></td>
                        </tr>
                        <tr>
                            <th scope="row"> Appareils connus</th>
                            <td><?php echo(count(json_decode($_SESSION['Data']['known_devices'], true))) ?></td>
                            <td><a href="#" data-toggle="modal" data-target="#modalDeleteKnownDevices"><i class='fas fa-minus-circle delete' title='Supprimer tous les appareils connus'></i></a></td>
                        </tr>
                    <thead class="thead-light">
                        <tr>
                            <th scope="col" style="width: 45%;">PRÉFÉRENCES</th>
                            <th scope="col" style="width: 45%;"></th>
                            <th scope="col" style="width: auto;"></th>
                        </tr>
                    </thead>
                        <tr>
                            <th scope="row"> Reception des alertes de sécurité par e-mail</th>
                            <td></td>
                            <td><a href="#" style="cursor: not-allowed;"><i class="fas fa-toggle-on enabled" style="cursor: not-allowed;" title="Non désactivable"></i></a></td>
                        </tr>
                        <tr>
                            <th scope="row"> Reception des notifications par e-mail</th>
                            <td></td>
                            <td><a href="#"><i class="fas fa-toggle-off disabled" title="Activer"></i></a></td>
                        </tr>
                    <thead class="thead-light">
                        <tr>
                            <th scope="col" style="width: 45%;">ACTIONS</th>
                            <th scope="col" style="width: 45%;"></th>
                            <th scope="col" style="width: auto;"></th>
                        </tr>
                    </thead>
                        <tr>
                            <th scope="row"> Visualiser mes données conservées par KI-OUI</th>
                            <td></td>
                            <td><i class="fas fa-eye edit" title="Visualiser"></i></td>
                        </tr>
                        <tr>
                            <th scope="row"> Télecharger une copie des mes données conservées par KI-OUI</th>
                            <td></td>
                            <td><a href="#" data-toggle="modal" data-target="#modalDlData"><i class="fas fa-download edit" title="Télecharger"></i></a></td>
                        </tr>
                        <tr>
                            <th scope="row"> Clôturer mon compte et supprimer toutes mes données</th>
                            <td></td>
                            <td><a href="#" data-toggle="modal" data-target="#modalDeleteAccountProcedure"><i class="fas fa-window-close delete" title="Fermer mon compte"></i></a></td>
                        </tr>
                    </tbody>
                </table>


            </div>
            </section>

        </div>
    </div>

</div>

<?php
$backupKey = randomString(16); 
include("./includes/pages/modals/backup-key.php");
$_SESSION['totp'] = ($_SESSION['Data']['totp'] == "" && !isset($_SESSION['totp'])) ? $ga->createSecret() : $_SESSION['totp'];
$_SESSION['totp'] = ($_SESSION['Data']['totp'] != "" && !isset($_SESSION['totp'])) ? $_SESSION['Data']['totp'] : $_SESSION['totp'];
include("./includes/pages/modals/totp.php");
include("./includes/pages/modals/download-data.php");
include("./includes/pages/modals/change-password.php");
include("./includes/pages/modals/change-username.php");
include("./includes/pages/modals/change-email.php");
include("./includes/pages/modals/delete-known-devices.php");
include("./includes/pages/modals/delete-account-procedure.php");
?>