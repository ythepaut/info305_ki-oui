<?php

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
 * Envoyer un e-mail sans passer par PHPMailer (Expediteur visible, sans html). (Eviter d'utiliser dans la mesure du possible)
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



?>