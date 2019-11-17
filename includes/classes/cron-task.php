<?php

include_once(getcwd() . "/config-db.php");
include_once(getcwd() . "/config-email.php");
include_once(getcwd() . "/utils.php");
require_once(getcwd() . '/PHPMailer/PHPMailerAutoload.php');


//Executions des tâches
deleteFilesByRules($connection);



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
