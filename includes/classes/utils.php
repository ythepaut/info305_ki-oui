<?php
define("AES_METHOD", "AES-256-CBC");
define("SIZE_FILE_NAME", 16);
define("TARGET_DIR", "../../uploads/");
define("TEMP_DIR", "../../uploads/tmp/");
define("MAX_FILE_SIZE", 50 * 10**6);
/**
 * Fonction qui retourne une chaîne de caractères aléatoire de longueur n.
 *
 * @param int           $n                  -   Longueur de la chaîne a generer
 *
 * @return string
 */
function randomString($n) {
    $characters = "0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
    $charactersLength = strlen($characters);
    $randomString = "";
    for ($i = 0; $i < $n; $i++) {
        $randomString .= $characters[random_int(0, $charactersLength - 1)];
    }
    return $randomString;
}
/**
 * Fonction chiffrant un texte à partir d'un mot de passe et d'un sel
 *
 * @param	string		$text						-	Texte à chiffrer
 * @param	string		$password					-	Mot de passe
 * @param	string		$salt						-	Sel, null par défaut,
 *
 * 														si null alors sera créé
 * @param	boolean		$raw						-	Si vrai alors retourne du binaire,
 * 														sinon retourne en base 64
 *
 * @return	array		$cryptedText, $salt, $hash	-	Liste contenant le texte crypté,
 * 														le sel utilisé et le hash du texte original
 */
function encryptText($text, $password, $salt = null, $raw = true) {
    if ($salt == null) {
        // $salt = hash_hmac('sha512', openssl_random_pseudo_bytes(64), $password, false);
        $salt = randomString(16);
    }
    $hash = hash_hmac('sha512', $text, $password . $salt, false);
    $initVector = substr($salt, 0, 16);
    $cryptedText = openssl_encrypt($text, AES_METHOD, $password . $salt, OPENSSL_RAW_DATA, $initVector);
    if (!$raw) {
        $cryptedText = base64_encode($cryptedText);
    }
    return array($cryptedText, $salt, $hash);
}
function decryptText($cryptedText, $password, $salt, $hash = null, $raw = true) {
    if (!$raw) {
        $cryptedText = base64_decode($cryptedText);
    }
    $initVector = substr($salt, 0, 16);
    $text = openssl_decrypt($cryptedText, AES_METHOD, $password . $salt, OPENSSL_RAW_DATA, $initVector);
    if ($hash === null || hash_equals(hash_hmac('sha512', $text, $password . $salt, false), $hash)) {
        return $text;
    }
    else {
        return null;
    }
}
/**
 * Fonction créant un fichier zip chiffré et l'ajoutant à la base de données
 *
 * @param 	mysqlconnection	$connection			- 	Connection à la base de données SQL
 * @param 	string			$content			-	Contenu du fichier à chiffrer
 * @param	int				$size				-	Taille du fichier
 * @param   string          $password           -   Clé de cryptage
 * @param	string			$oldName			-	Ancien nom du fcihier : nom original
 * @param 	string			$newName			-	Nouveau nom du fichier : facultatif, si null sera initialisé random
 *
 * @return	string			$newName			-	Nouveau nom du fichier
 */
function createCryptedZipFile($connection, $content, $size, $password, $oldName, $newName = null) {
    $compt = 0;
	if ($newName === null) {
		do {
			$newName = randomString(SIZE_FILE_NAME);
			$compt ++;
		} while (file_exists(TARGET_DIR.$newName) && $compt < 100);
	}
    if (file_exists(TARGET_DIR.$newName)) {
        return null;
    }

    $ownerId = $_SESSION["Data"]["id"];
    $ip = $_SERVER["REMOTE_ADDR"];
    $zipFile = new ZipArchive;
    if ($zipFile->open(TARGET_DIR.$newName, ZipArchive::CREATE) === TRUE) {
        $zipFile->addFromString($newName, $content);
        $zipFile->close();
    }
    else {
        throw new Exception("Le fichier zip n'a pas pu être créé");
    }
    $zipText = file_get_contents(TARGET_DIR.$newName);
    list($encryptedText, $salt, $hash) = encryptText($zipText, $password);
    file_put_contents(TARGET_DIR.$newName, $encryptedText);
    $query = $connection->prepare("INSERT INTO kioui_files (original_name, path, owner, salt, size, ip, content_hash) VALUES (?,?,?,?,?,?,?)");
    $query->bind_param("ssisiss", $oldName, $newName, $ownerId, $salt, $size, $ip, $hash);
    $query->execute();
    $query->close();
    return $newName;
}
/**
 * Fonction déchiffrant un fichier zip
 *
 * @param   mysqlconnection	$connection			- 	Connection à la base de données SQL
 * @param   string          $cryptedFileName    -   Nom du fichier chiffré (son nom sur le serveur)
 * @param   string          $key                -   Clé de déchiffrage
 *
 * @param   string          $content            -   Contenu déchiffré du fichier
 */
function unzipCryptedFile($connection, $cryptedFileName, $key) {
    $query = $connection->prepare("SELECT * FROM kioui_files WHERE path = ?");
    $query->bind_param("s", $cryptedFileName);
    $query->execute();
    $result = $query->get_result();
    $query->close();
    $fileData = $result->fetch_assoc();

    $zipTextEncrypted = file_get_contents(TARGET_DIR.$cryptedFileName);
    $zipText = decryptText($zipTextEncrypted, $_SESSION["UserPassword"], $fileData["salt"], $fileData["hash"]);

    if ($zipText === null) {
        die("Hash différent !");
    }
    else {
        file_put_contents(TEMP_DIR.$cryptedFileName, $zipText);

        $zipFile = new ZipArchive;
        if ($zipFile->open(TEMP_DIR.$cryptedFileName) === true) {
            $zipFile->extractTo(TEMP_DIR."zip/");
            $zipFile->close();
        }
        $content = file_get_contents(TEMP_DIR."zip/".$cryptedFileName);

        unlink(TEMP_DIR.$cryptedFileName);
        unlink(TEMP_DIR."zip/".$cryptedFileName);

        return $content;
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
/**
 * Fonction qui rafraichit la variable de session.
 *
 * @param mysqlconnection   $connection         -   Connexion BDD effectuée dans le fichier config-db.php
 *
 * @return void
 */
function refreshSession($connection) {
    $query = $connection->prepare("SELECT * FROM kioui_accounts WHERE id = ?");
    $query->bind_param("s", $_SESSION['Data']['id']);
    $query->execute();
    $result = $query->get_result();
    $query->close();
    $userData = $result->fetch_assoc();
    $_SESSION['Data'] = $userData;
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
 * Fonction qui renvoie l'espace occupé par un utilisateur
 *
 * @param string             $idUser   			-   identifiant de l'utilisateur
 * @param mysqlconnection    $connection        -   Connexion BDD effectuée dans le fichier config-db.php
 *
 * @return integer                              -   Espace occupé par l'utilisateur en octets
 */
function getSize($idUser, $connection){
	$size = 0;

    //on récupère tout les fichiers

    $files = getFiles($idUser, $connection);
    foreach ($files as $file){
        $size += $file['size'];
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
function getFiles($idUser, $connection) {
	$filesUser=[];
	//On récupère tout les fichiers
    $files = mysqli_query($connection, "SELECT * FROM kioui_files");
    while ($folder = mysqli_fetch_assoc($files)) {
        if ($folder['owner'] == $idUser) {
            $filesUser[] = $folder;
        }
    }
    return $filesUser;
}
/**
 * Fonction qui renvoie la conversion d'une taille de fichier en octets en une chaine de charactères avec les unitées
 *
 * @param interger             $size       		- taille en octets d'un fichier
 *
 * @return string
 */
function convertUnits($size){
	$unit = "";
	$stringSize = NULL;

	if (floor($size/10**6) > 0){
		$unit = " Mo";
		$stringSize = round($size/10**6, 2);
	} else if (floor($size/10**3) > 0){
		$unit = " Ko";
		$stringSize = round($size/10**3, 2);
	} else{
		$unit = " octets";
		$stringSize = $size;
	}
	$stringSize = ((string) $stringSize) . $unit;
	return $stringSize;
}
/**
 * Fonction qui change le mot de passe d'un utilisateur donné
 *
 * @param integer             $userId       		- id de l'utilisateur
 * @param string			  $oldPassword			- ancien mot de passe
 * @param string			  $newPassword			- nouveau mot de passe
 * @param mysqlconnection     $connection           - Connexion BDD effectuée dans le fichier config-db.php
 *
 * @return string
*/
function changePassword($userId, $oldPassword, $newPassword, $connection) {
	$result = '';
	if (isset($userId, $oldPassword, $newPassword) && $userId != "" && $oldPassword != "" && $newPassword != "") {
		//Recuperation des données
		$query = $connection->prepare("SELECT * FROM kioui_accounts WHERE email = ?");
		$query->bind_param("s", $email);
		$query->execute();
		$result = $query->get_result();
		$query->close();
		$userData = $result->fetch_assoc();

		//Identifiants correct ?
		if (isset($userData['id']) && $userData['id'] != null && password_verify(hash('sha512', $oldPassword . $userData['salt']), $userData['password'])) {
			//nouveau mot de passe correct ?
			if (strlen($newPassword) >= 8 && preg_match("#[0-9]+#", $newPassword) && preg_match("#[a-zA-Z]+#", $newPassword)) {
				//décrypter et rencrypter tous les fichiers
				$files = getFiles($idUser,$connection);
                //obtenir les deux clés de décryptage et de cryptage
				$oldUserKey = hash('sha512', $oldPassword . $userData['salt']);
                $newUserKey = hash('sha512', $newPassword . $userData['salt']);

                foreach ($files as $file) {
                    $content = unzipCryptedFile($connection, $file['path'], $oldUserKey);
                    createCryptedZipFile($connection, $content, $file['size'], $newPassword, $file['original_name']);
				}
			}
		} else {
		}
	} else {
		$result = "ERROR_MISSING_FIELDS#Veuillez remplir tous les champs.";
	}
}
/**
 * Fonction qui renvoie le nombre total d'utilisateurs stockés
 *
 * @param mysqlconnection    $connection        -   Connexion BDD effectuée dans le fichier config-db.php
 *
 * @return integer
 */
function getNbUsers($connection) {
    $result = mysqli_query($connection, "SELECT * FROM kioui_accounts");
    return mysqli_num_rows ( $result );
}
/**
 * Fonction qui renvoie le nombre total de fichiers stockés
 *
 * @param mysqlconnection    $connection        -   Connexion BDD effectuée dans le fichier config-db.php
 *
 * @return integer
 */
function getNbFiles($connection) {

    $result = mysqli_query($connection, "SELECT * FROM kioui_files");
    return mysqli_num_rows ( $result );
}
/**
 * Fonction qui renvoie la taille totale des fichiers stockés
 *
 * @param mysqlconnection    $connection        -   Connexion BDD effectuée dans le fichier config-db.php
 *
 * @return string
 */
function getNbSize($connection) {
    $result = mysqli_query($connection, "SELECT * FROM kioui_files");
    $sum = 0;
    while ($row = mysqli_fetch_assoc($result)) {
        $sum += $row['size'];
    }
    return convertUnits($sum);
}
/**
 * Fonction qui génère le lien qui permet de décoder un fichier spécifique
 *
 * @param string              $password       		- mot de passe de l'utilisateur hash(mdp + sel_user)
 * @param integer  			  $fileId  				- id du fichier
 * @param mysqlconnection     $connection           - Connexion BDD effectuée dans le fichier config-db.php
 *
 * @return string
*/
function generateDlLink($password, $fileId, $connection) {
	$result='';
	if (isset($password, $fileId, $connection) && $password!='' && $fileId!='' && $connection!='') {
		//récupération infos fichier
		$query = $connection->prepare("SELECT * FROM kioui_files WHERE id = ?");
        $query->bind_param("s", $fileId);
        $query->execute();
        $result = $query->get_result();
        $query->close();
        $file = $result->fetch_assoc();
		//génération du lien
		$fileName=$file['path'];
		$fileSalt=$file['salt'];
		$result= "https://ki-oui.ythepaut.com/dl/".$fileName."/".$fileSalt;
	} else {
		$result = "ERROR_MISSING_VARIABLES#Veuillez entrer toutes les variables.";
	}
	return $result;
}
// Format du lien : https://ki-oui.ythepaut.com/dl/{NOM_FICHIER}/{CLE_DECRYPTAGE}

?>
