<?php

define("AES_METHOD", "AES-256-CBC");
define("SIZE_FILE_NAME", 64);
define("TARGET_DIR", "../../uploads/");
define("TEMP_DIR", "../../uploads/tmp/");
define("MAX_FILE_SIZE", 50 * 10**6);

/**
 * Fonction qui retourne une chaîne de caractères aléatoire de longueur n.
 *
 * @param int           $n                  -       Longueur de la chaîne a generer
 *
 * @return string
 */
function randomString($n) {
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $randomString = '';
    for ($i = 0; $i < $n; $i++) {
        $randomString .= $characters[rand(0, $charactersLength - 1)];
    }
    return $randomString;
}

function encryptText($text, $key, $initVector = null) {
    if ($initVector == null) {
        $initVector = substr(hash_hmac('sha256', openssl_random_pseudo_bytes(64), $key, false), 0, 16);
    }

    $cryptedText = openssl_encrypt($text, AES_METHOD, $key, OPENSSL_RAW_DATA, $initVector);
    $hash = hash_hmac('sha256', $initVector . $text, $key, true);

    return array($cryptedText, $initVector, $hash);
}

function decryptText($cryptedText, $key, $initVector, $hash) {
    $text = openssl_decrypt($cryptedText, AES_METHOD, $key, OPENSSL_RAW_DATA, $initVector);

    if ($hash === null || hash_equals(hash_hmac('sha256', $initVector . $text, $key, true), $hash)) {
        return $text;
    }
    else {
        return null;
    }
}

/**
 * Fonction qui retourne la source relative d'un fichier en fonction de l'emplacement actuel.
 *
 * @param string        $relative_src       -       Chemin d'accès relatif par défaut
 *
 * @return string
 */
function getSrc($relative_src) {
    $result_src = $relative_src;
    $nb = substr_count($_SERVER['REQUEST_URI'], "/", 0, strlen($_SERVER['REQUEST_URI']));

    return str_repeat("../", $nb - 1) . "." . $relative_src;
}


if (file_exists(getcwd() . '/includes/classes/PHPMailer/PHPMailerAutoload.php')) {
    require_once(getcwd() . '/includes/classes/PHPMailer/PHPMailerAutoload.php');
}
/**
 * Envoyer un e-mail.
 *
 * @param array         $em         -   Identifiants de compte e-mail d'envoi de notifications.
 * @param string        $to         -   Adresse e-mail destination.
 * @param string        $subject    -   Sujet du message.
 * @param string        $title      -   Titre du corps du message.
 * @param string        $body       -   Texte du message.
 * @param string        $button_link-   Lien du bouton.
 * @param string        $button_text-   Texte du bouton.
 *
 * @return void
 */
function sendMail($em, $to, $subject, $title, $body, $button_link, $button_text) {

	$message = file_get_contents(getcwd() . "/PHPMailer/email_format/email-layout1.php");
	$message .= $title . file_get_contents(getcwd() . "/PHPMailer/email_format/email-layout2.php");
	$message .= $body . file_get_contents(getcwd() . "/PHPMailer/email_format/email-layout3.php");
	$message .= $button_link . file_get_contents(getcwd() . "/PHPMailer/email_format/email-layout4.php");
	$message .= $button_text . file_get_contents(getcwd() . "/PHPMailer/email_format/email-layout5.php");



	$phpmail = new PHPMailer(true);
	try {


		//Server settings
		$phpmail->SMTPDebug = 0;                                 // Enable verbose debug output
		$phpmail->isSMTP();                                      // Set mailer to use SMTP
		$phpmail->Host = 'SSL0.OVH.NET';  						 // Specify main and backup SMTP servers
		$phpmail->SMTPAuth = true;                               // Enable SMTP authentication
		$phpmail->Username = $em['address'];                 // SMTP username
		$phpmail->Password = $em['password'];                           // SMTP password
		$phpmail->SMTPSecure = 'ssl';  //tls/ssl                          // Enable TLS encryption, `ssl` also accepted
		$phpmail->Port = 465;//587//465                                    // TCP port to connect to

		//Recipients
		$phpmail->setFrom('ki-oui@ythepaut.com', 'KI-OUI');
		$phpmail->addAddress($to);
		$phpmail->addReplyTo('noreply@ythepaut.com', 'Ne pas repondre');

		//Content
		$phpmail->isHTML(true);                                  // Set email format to HTML
		$phpmail->CharSet = 'UTF-8';
		$phpmail->Subject = $subject;
		$phpmail->Body    = $message;
		$phpmail->AltBody = $message;

		$phpmail->send();
	} catch (Exception $e) {
	}

}


/**
 * Envoyer un e-mail sans passer par PHPMailer (Expediteur ovh visible, sans html). (Eviter d'utiliser dans la mesure du possible)
 *
 * @param string        $to         -   Adresse e-mail destination.
 * @param string        $subject    -   Sujet du message.
 * @param string        $message    -   Texte du message.
 *
 * @return void
 */
function sendMailwosmtp($to,$subject,$message) {

	$headers = "From: KI-OUI <ki-oui@ythepaut.com>\r\n";
	$headers .= "Reply-To: noreply@ythepaut.com\r\n";

	mail($to, $subject, $message, $headers);

}

/**
 * Fonction qui renvoie l'espace occuper par un utilisateur
 *
 * @param string             $idUser   			-   identifiant de l'utilisateur
 * @param mysqlconnection    $connection        -   Connexion BDD effectuée dans le fichier config-db.php
 *
 * @return integer                              -   Espace occupé par l'utilisateur en octets
 */
function getSize($idUser,$connection){
	$size=0;
    //on récupère tout les fichiers
    $folders = getFolders($idUser,$connection);

    foreach($folders as $folder){
        $size+=$folder['size'];
    }

    return $size;
}

/**
 * Fonction qui renvoie les fichiers de l'utilisateur
 *
 * @param string             $idUser   			-   identifiant de l'utilisateur
 * @param mysqlconnection    $connection        -   Connexion BDD effectuée dans le fichier config-db.php
 *
 * @return array
 */
function getFolders($idUser,$connection){
	$foldersUser=[];
	//on récupère tout les fichiers
    $folders = mysqli_query($connection, "SELECT * FROM kioui_files");

    while ($folder = mysqli_fetch_assoc($folders)) {
        if($folder['owner']==$idUser){
            $foldersUser[]=$folder;
        }
    }

    return $foldersUser;
}
/**
 * Fonction qui renvoie la conversion d'une taille de fichier en octets en une chaine de charactères avec les unitées
 *
 * @param interger             $size       		- taille en octets d'un fichier
 *
 * @return string
 */
function convertUnits($size){
	$unit='';
	$stringSize=NULL;
	if (floor($size/10**6) > 0){
		$unit=' Mo';
		$stringSize=round($size/10**6,2);
	}
	else if (floor($size/10**3) > 0){
		$unit=' Ko';
		$stringSize=round($size/10**3,2);
	}
	else{
		$unit=' octets';
		$stringSize=$size;
	}

	$stringSize=((string)$stringSize).$unit;

	return $stringSize;
}
?>
