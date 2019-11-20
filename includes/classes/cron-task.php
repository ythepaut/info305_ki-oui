<?php

include_once(getcwd() . "/config-db.php");
include_once(getcwd() . "/config-email.php");
include_once(getcwd() . "/utils.php");
require_once(getcwd() . '/PHPMailer/PHPMailerAutoload.php');


//Executions des tâches
deleteFilesByRules($connection);
deleteAccountProcedure($connection, $em);



/**
 * Fonction qui supprimme les fichiers en fonction de leurs limites (telechargement, temps ...)
 * (Tâche CRON)
 *
 * @param mysqlconnection   $connection         -   Connexion BDD effectuée dans le fichier config-db.php
 *
 * @return void
 */
function deleteFilesByRules($connection) {

    $query = "SELECT * FROM kioui_files";
    $results = mysqli_query($connection, $query);

    while ($file = mysqli_fetch_assoc($results)) {

        //Nb de telechargements dépassé ?
        if ($file['download_count'] >= $file['download_limit'] && $file['download_limit'] != "0") {
            deleteFile($file['id'], $connection);
        }

        //Fichier expiré ?
        if ($file['erase_date'] >= time() && $file['erase_date'] != "0") {
            deleteFile($file['id'], $connection);
        }

    }

}

/**
 * Fonction qui gere la cloturation des comptes
 * (Tâche CRON)
 * 
 * @param mysqlconnection     $connection           - Connexion BDD effectuée dans le fichier config-db.php
 * @param array               $em                   -   Identifiants email dans le fichier config-email.php
 */
function deleteAccountProcedure($connection, $em) {
    //TODO : Envoi email J-1 et H-0.

    $query = "SELECT * FROM kioui_accounts WHERE status = 'DELETE_PROCEDURE'";
    $results = mysqli_query($connection, $query);


    while ($account = mysqli_fetch_assoc($results)) {

        if ($account['access_level'] != "ADMINISTRATOR" && $account['account_expire'] < time()) { //Supprimer ?

            $queryFiles = "SELECT * FROM kioui_files WHERE owner = " . $account['id'];
            $resultsFiles = mysqli_query($connection, $query);

            //Suppression des fichiers
            while ($file = mysqli_fetch_assoc($resultsFiles)) {
                deleteFile($file['id'], $connection);
            }

            //Suppression du compte
            $query = $connection->prepare("DELETE FROM kioui_accounts WHERE id = ?");
            $query->bind_param("i", $account['id']);
            $query->execute();
            $query->close();

        }

    }

}



/**
 * Fonction qui supprime un fichier.
 *
 * @param integer           $id                 -   ID du fichier à supprimer
 * @param mysqlconnection   $connection         -   Connexion BDD effectuée dans le fichier config-db.php
 *
 * @return void
 */
function deleteFile($id, $connection) {
    //Acquisition du fichier
    $query = $connection->prepare("SELECT * FROM kioui_files WHERE id = ?");
    $query->bind_param("i", $id);
    $query->execute();
    $results = $query->get_result();
    $query->close();
    $fileData = $results->fetch_assoc();

    $filePath = $fileData['path'];

    if (isset($filePath) && $filePath != "") {

        if (unlink(UPLOAD_DIR.$filePath)) {

            //Suppression de la BDD
            $query = $connection->prepare("DELETE FROM kioui_files WHERE id = ?");
            $query->bind_param("i", $id);
            $query->execute();
            $query->close();
        }
    }

}

?>
