<?php
define("AES_METHOD", "AES-256-CBC");
define("SIZE_FILE_NAME", 16);
define("UPLOAD_DIR", "../../../live/uploads/");
define("TEMP_DIR", "../../../live/tmp/");
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
 * @param    string        $text                        -   Texte à chiffrer
 * @param    string        $password                    -   Mot de passe
 * @param    string        $salt                        -   Sel, null par défaut,
 *                                                          si null alors sera créé
 * @param    boolean       $raw                         -   Si vrai alors retourne du binaire,
 *                                                          sinon retourne en base 64
 *
 * @return    array        $cryptedText, $salt, $hash   -   Liste contenant le texte crypté,
 *                                                          le sel utilisé et le hash du texte original
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

/**
 * Fonction déchiffrant un texte à partir d'un mot de passe et d'un sel
 *
 * @param    string        $cryptedText                 -   Texte à déchiffrer
 * @param    string        $password                    -   Mot de passe
 * @param    string        $salt                        -   Sel
 * @param    string        $hash                        -   Hash du texte déchiffré, si les hash ne correspondent pas
 *                                                          et que hast != null alors la fonction renvoie null
 * @param    boolean       $raw                         -   Si vrai alors texte original en binaire,
 *                                                          sinon en base 64
 *
 * @return   string        $text                        -   Texte déchiffré
 */
function decryptText($cryptedText, $password, $salt, $hash = null, $raw = true) {
    if (!$raw) {
        $cryptedText = base64_decode($cryptedText);
    }
    $initVector = substr($salt, 0, 16);
    $text = openssl_decrypt($cryptedText, AES_METHOD, $password . $salt, OPENSSL_RAW_DATA, $initVector);
    if ($hash === null || hash_equals(hash_hmac('sha512', $text, $password . $salt, false), $hash)) {
        return $text;
    } else {
        return null;
    }
}


/**
 * Fonction créant un fichier zip chiffré et l'ajoutant à la base de données
 *
 * @param   mysqlconnection $connection         -   Connection à la base de données SQL
 * @param   string          $content            -   Contenu du fichier à chiffrer
 * @param   int             $size               -   Taille du fichier
 * @param   string          $password           -   Clé de cryptage
 * @param   string          $oldName            -   Ancien nom du fichier : nom original
 * @param   string          $newName            -   Nouveau nom du fichier : facultatif, si null sera initialisé random
 *
 * @return  string          $newName            -   Nouveau nom du fichier
 */
function createCryptedZipFile($connection, $content, $size, $password, $oldName, $newName = null) {
    $compt = 0;
    if ($newName === null) {
        do {
            $newName = randomString(SIZE_FILE_NAME);
            $compt ++;
        } while (file_exists(UPLOAD_DIR.$newName) && $compt < 100);
    }
    if (file_exists(UPLOAD_DIR.$newName)) {
        return null;
    }

    $ownerId = $_SESSION["Data"]["id"];
    $ip = $_SERVER["REMOTE_ADDR"];
    $zipFile = new ZipArchive;
    if ($zipFile->open(UPLOAD_DIR.$newName, ZipArchive::CREATE) === TRUE) {
        $zipFile->addFromString($newName, $content);
        $zipFile->close();
    } else {
        throw new Exception("Le fichier zip n'a pas pu être créé");
    }
    $zipText = file_get_contents(UPLOAD_DIR.$newName);

    list($oldName, $salt, $hash) = encryptText($oldName, $password, null, false);
    list($encryptedText, $salt, $hash) = encryptText($zipText, $password, $salt);

    $uploadDate = time();

    file_put_contents(UPLOAD_DIR.$newName, $encryptedText);
    $query = $connection->prepare("INSERT INTO kioui_files (original_name, path, owner, salt, size, ip, content_hash, upload_date) VALUES (?,?,?,?,?,?,?,?)");
    $query->bind_param("ssisissi", $oldName, $newName, $ownerId, $salt, $size, $ip, $hash, $uploadDate);
    $query->execute();
    $query->close();

    return $newName;
}


/**
 * Fonction déchiffrant un fichier zip
 *
 * @param   mysqlconnection $connection         -   Connection à la base de données SQL
 * @param   string          $cryptedFileName    -   Nom du fichier chiffré (son nom sur le serveur)
 * @param   string          $key                -   Clé de déchiffrage
 *
 * @return  string          $content            -   Contenu déchiffré du fichier
 * @return  string          $name               -   Nom du fichier déchiffré
 */
function unzipCryptedFile($connection, $cryptedFileName, $key) {
    if (!file_exists(UPLOAD_DIR.$cryptedFileName)) {
        return null;
    }

    $query = $connection->prepare("SELECT * FROM kioui_files WHERE path = ?");
    $query->bind_param("s", $cryptedFileName);
    $query->execute();
    $result = $query->get_result();
    $query->close();
    $fileData = $result->fetch_assoc();

    $name = decryptText($fileData["original_name"], $key, $fileData["salt"], null, false);

    $zipTextEncrypted = file_get_contents(UPLOAD_DIR.$cryptedFileName);
    $zipText = decryptText($zipTextEncrypted, $key, $fileData["salt"], $fileData["hash"]);

    if ($zipText !== null) {
        file_put_contents(TEMP_DIR.$cryptedFileName, $zipText);

        $zipFile = new ZipArchive;
        if ($zipFile->open(TEMP_DIR.$cryptedFileName) === true) {
            $zipFile->extractTo(TEMP_DIR."zip/");
            $zipFile->close();
        }
        $content = file_get_contents(TEMP_DIR."zip/".$cryptedFileName);

        unlink(TEMP_DIR.$cryptedFileName);
        unlink(TEMP_DIR."zip/".$cryptedFileName);

        return array($content, $name);
    }

    return array(null, null);
}


/**
 * Fonction qui actualise la clé ou le sel d'un fichier donné
 * 
 * @param   mysqlconnection $connection         -   Connection à la base de données SQL 
 * @param   string          $cryptedFileName    -   le nom du fichier encrypté
 * @param   string          $userKey            -   la clé de décryptage de l'utilisateur
 * @param   string          $newUserKey         -   Facultatif: la nouvelle clé de cryptage de l'utilisateur
 * @param   string          $newSalt            -   Facultatif: le nouveau sel du fichier
 * 
 * @return  void
 */
function updateEncryption($cryptedFileName, $userKey, $connection, $newKey = "", $newSalt = "") {
    if ($newKey != "" || $newSalt != "") {

        //récupération des données du fichier
        $query = $connection->prepare("SELECT * FROM kioui_files WHERE original_name = ?");
        $query->bind_param("s", $cryptedFileName);
        $query->execute();
        $result = $query->get_result();
        $query->close();
        $fileData = $result->fetch_assoc();

        if (file_exists(UPLOAD_DIR.$fileData['path']) && $fileData != null) {

            if ($newKey == ""){
                $newKey = $userKey;
            }
            if ($newSalt == ""){
                $newSalt = $fileData['salt'];
            }

            $name = decryptText($fileData["original_name"], $userKey, $fileData["salt"], null, false);
            
            $zipTextEncrypted = file_get_contents(UPLOAD_DIR.$fileData['path']);
            $zipText = decryptText($zipTextEncrypted, $userKey, $fileData["salt"], $fileData["hash"]);


            if ($zipText !== null) {
                //réencryptage des données
                list($newName, $salt, $hash) = encryptText($name, $newKey, $newSalt, false);
                list($encryptedText, $salt, $hash) = encryptText($zipText, $newKey, $salt); 

                //update du contenu du fichier
                file_put_contents(UPLOAD_DIR.$fileData['path'], $encryptedText);
                if (!file_exists(UPLOAD_DIR.$fileData['path'])){
                    throw new Exception ("echec de l'ouverture du fichier");
                }

                //update de la BDD
                $query = $connection->prepare("UPDATE kioui_files SET original_name = ?  , salt = ? , content_hash = ? WHERE original_name = ?");
                $query->bind_param("ssss", $newName, $salt, $hash, $cryptedFileName);
                $query->execute();
                $query->close();

            } else {
                throw new Exception("le fichier est vide");
            }

        } else {
            throw new Exception("Le fichier n'existe pas");
        }

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


/**
 * Fonction retourne si la session est valide ou non
 *
 * @param mysqlconnection   $connection         -   Connexion BDD effectuée dans le fichier config-db.php
 *
 * @return boolean
 */
function isValidSession($connection) {
    refreshSession($connection);
    return $_SESSION['Data']['status'] == "ALIVE" && $_SESSION['LoggedIn'] && $_SESSION['tfa'] == "trusted";
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
        $phpmail->Host = 'SSL0.OVH.NET';                           // Specify main and backup SMTP servers
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
function sendMailwosmtp($to, $subject, $message) {
    $headers = "From: KI-OUI <ki-oui@ythepaut.com>\r\n";
    $headers .= "Reply-To: noreply@ythepaut.com\r\n";
    mail($to, $subject, $message, $headers);
}


/**
 * Fonction qui renvoie l'espace occupé par un utilisateur
 *
 * @param string             $idUser               -   identifiant de l'utilisateur
 * @param mysqlconnection    $connection        -   Connexion BDD effectuée dans le fichier config-db.php
 *
 * @return integer                              -   Espace occupé par l'utilisateur en octets
 */
function getSize($idUser, $connection) {
    $size = 0;

    //Acquisiton des fichiers

    $files = getFiles($idUser, $connection);
    foreach ($files as $file) {
        $size += $file['size'];
    }

    return $size;
}


/**
 * Fonction qui renvoie les fichiers de l'utilisateur
 *
 * @param string             $idUser               -   identifiant de l'utilisateur
 * @param mysqlconnection    $connection        -   Connexion BDD effectuée dans le fichier config-db.php
 * @param string             $sort              -   Trie en fct de la colonne
 *
 * @return array
 */
function getFiles($idUser, $connection, $sort = 'id/DESC') {
    $filesUser=[];
    //Acquisiton des fichiers
    $query = mysqli_query($connection, "SELECT * FROM kioui_files ORDER BY " . explode("/", $sort)[0] . " " . explode("/", $sort)[1]);
    while ($file = mysqli_fetch_assoc($query)) {
        if ($file['owner'] == $idUser) {
            $filesUser[] = $file;
        }
    }
    return $filesUser;
}

/**
 * Fonction qui renvoie la conversion d'une taille de fichier en octets en une chaine de charactères avec les unitées
 *
 * @param interger             $size               - taille en octets d'un fichier
 *
 * @return string
 */
function convertUnits($size) {
    $unit = "";
    $stringSize = NULL;

    if (floor($size/10**24) > 0) {
        $unit = " Yo";
        $stringSize = round($size/10**24, 2);
    }
    else if (floor($size/10**21) > 0) {
        $unit = " Zo";
        $stringSize = round($size/10**21, 2);
    }
    else if (floor($size/10**18) > 0) {
        $unit = " Eo";
        $stringSize = round($size/10**18, 2);
    }
    else if (floor($size/10**15) > 0) {
        $unit = " Po";
        $stringSize = round($size/10**15, 2);
    }
    else if (floor($size/10**12) > 0) {
        $unit = " To";
        $stringSize = round($size/10**12, 2);
    }
    else if (floor($size/10**9) > 0) {
        $unit = " Go";
        $stringSize = round($size/10**9, 2);
    }
    else if (floor($size/10**6) > 0) {
        $unit = " Mo";
        $stringSize = round($size/10**6, 2);
    } else if (floor($size/10**3) > 0) {
        $unit = " Ko";
        $stringSize = round($size/10**3, 2);
    } else {
        $unit = " o";
        $stringSize = $size;
    }
    $stringSize = ((string) $stringSize) . $unit;
    return $stringSize;
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
    return mysqli_num_rows($result);
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
    return mysqli_num_rows($result);
}


/**
 * Fonction qui renvoie une colonne des stats de ki-oui
 *
 * @param string             $stat              -   Statistique à acquerir
 * @param mysqlconnection    $connection        -   Connexion BDD effectuée dans le fichier config-db.php
 *
 * @return integer
 */
function getStats($stat, $connection) {
    $query = $connection->prepare("SELECT * FROM kioui_stats WHERE id = 0");
    $query->execute();
    $result = $query->get_result();
    $query->close();
    $stats = $result->fetch_assoc();
    return $stats[$stat];
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
 * Génère un lien de partage de fichier
 *
 * @param       string      $password           -   Mot de passe du fichier
 * @param       int         $fileId             -   id dans la BDD du fichier
 * @param   mysqlconnection $connection         -   Connexion à la BDD
 *
 * @return      string                          -   URL de partage
 */
function generateShareLink($password, $fileId, $connection) {
    return generateDlLink($password, $fileId, $connection, $base = "share-file");
}


/**
 * Fonction qui génère le lien qui permet de décoder un fichier spécifique
 *
 * @param string              $password               - mot de passe de l'utilisateur hash(mdp + sel_user)
 * @param integer             $fileId                 - id du fichier
 * @param mysqlconnection     $connection             - Connexion BDD effectuée dans le fichier config-db.php
 *
 * @return string
*/
function generateDlLink($password, $fileId, $connection, $base = "dl") {
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
        $fileName = $file['path'];
        $filePassword = $_SESSION['UserPassword'];
        $result= "https://ki-oui.com/" . $base . "/" . $fileName . "/" . $filePassword;
    } else {
        $result = "ERROR_MISSING_VARIABLES#Veuillez entrer toutes les variables.";
    }
    return $result;
}
// Format du lien de téléchargement : https://ki-oui.com/dl/{NOM_FICHIER}/{CLE_DECRYPTAGE}

/**
 * Télécharge un fichier
 * Cette fonction peut télécharger depuis fichier (laissant $name et $from_string par défaut),
 * ou peut télécharger depuis une chaine de caractères, il faudra alors préciser le nom du fichier
 * pour l'utilisateur et mettre $from_string à true.
 *
 * @param   string      $content        - Contenu du fichier, ou son nom
 * @param   string      $name           - Nom du ficihier lors du téléchargement
 * @param   boolean     $from_string    - Si true, $content est le contenu à télécharger,
 *                                        sinon $content est le nom du fichier à télécharger
 */
function downloadFile($content, $name = null, $from_string = false) {
    if ($from_string) {
        $file = TEMP_DIR . randomString(16);
        file_put_contents($file, $content);
    } else {
        $file = $content;
    }

    if ($name === null) {
        $name = basename($file);
    }

    $knownMimeTypes = array(
        "htm"  => "text/html",
        "exe"  => "application/octet-stream",
        "zip"  => "application/zip",
        "doc"  => "application/msword",
        "jpg"  => "image/jpg",
        "php"  => "text/plain",
        "xls"  => "application/vnd.ms-excel",
        "ppt"  => "application/vnd.ms-powerpoint",
        "gif"  => "image/gif",
        "pdf"  => "application/pdf",
        "txt"  => "text/plain",
        "html" => "text/html",
        "png"  => "image/png",
        "jpeg" => "image/jpg",
    );

    $fileExtension = pathinfo($file, PATHINFO_EXTENSION);

    if (array_key_exists($fileExtension, $knownMimeTypes)) {
        $mimeType = $knownMimeTypes[$fileExtension];
    } else {
        $mimeType = "application/octet-stream";
    }

    if (file_exists($file)) {
        @ob_end_clean();
        header('Content-Description: File Transfer');
        header('Content-Type: ' . $mimeType);
        header('Content-Disposition: attachment; filename="' . $name . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));

        readfile($file);

        if ($from_string) {
            unlink($file);
        }
    } else {
        header("Location : /404");
    }
}

function downloadFileOld($filename, $name, $mimeType='') {
    $size = filesize($filename);
    $name = rawurldecode($name);

    $knownMimeTypes = array(
        "htm"  => "text/html",
        "exe"  => "application/octet-stream",
        "zip"  => "application/zip",
        "doc"  => "application/msword",
        "jpg"  => "image/jpg",
        "php"  => "text/plain",
        "xls"  => "application/vnd.ms-excel",
        "ppt"  => "application/vnd.ms-powerpoint",
        "gif"  => "image/gif",
        "pdf"  => "application/pdf",
        "txt"  => "text/plain",
        "html" => "text/html",
        "png"  => "image/png",
        "jpeg" => "image/jpg",
    );

    if ($mimeType == '') {
        $fileExtension = pathinfo($filename, PATHINFO_EXTENSION);

        if (array_key_exists($fileExtension, $knownMimeTypes)) {
            $mimeType = $knownMimeTypes[$fileExtension];
        } else {
            $mimeType = "application/force-download";
        }
    }

    /*
    @ob_end_clean();
    header("Content-Type: " . $mimeType);
    header('Content-Disposition: attachment; filename="' . $name . '"');
    header("Content-Transfer-Encoding: binary");
    header("Accept-Ranges: bytes");

    header("Content-Length: " . $size);
    */
    $chunksize = 1*1024*1024;
    $bytes_send = 0;

    $file = fopen($filename, "rb");

    if ($file) {
        while (!feof($filename) && !connection_aborted() && $bytes_send < $size) {
            $buffer = fread($filename, $chunksize);
            echo($buffer);
            flush();
            $bytes_send += strlen($buffer);
        }

        fclose($filename);
    } else {
        die("Erreur : impossible d'ouvrir le fichier");
    }
}


/**
 * Fonction retourne le temps ecoulé sous forme de chaine de caractères. e.g : "Il y a 3 minutes"
 *
 * @param integer             $datetime             - Timestamp de reference
 * @param boolean             $full                 - Retourner la chaine complete ? e.g : "Il y a 2 mois, 12 jours, 4 heures, 8 minutes et 48 secondes"
 *
 * @return string
*/
function time_elapsed_string($datetime, $full = false) {
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    $diff->w = floor($diff->d / 7);
    $diff->d -= $diff->w * 7;

    $string = array(
        'y' => 'an',
        'm' => 'mois',
        'w' => 'semaine',
        'd' => 'jour',
        'h' => 'heure',
        'i' => 'minute',
        's' => 'seconde',
    );
    foreach ($string as $k => &$v) {
        if ($diff->$k) {
            $v = $diff->$k . ' ' . $v . ($diff->$k > 1 ? 's' : '');
        } else {
            unset($string[$k]);
        }
    }

    if (!$full) $string = array_slice($string, 0, 1);
    return $string ? 'Il y a ' . implode(', ', $string) : 'A l\'instant';
}


/**
 * Fonction qui retourne l'url d'une image en fct d'une extension de fichier
 *
 * @param string             $extension             - Extension du fichier
 *
 * @return string
 */
function extensionImage($extension) {
    $result = "https://img.icons8.com/dusk/256/000000/file--v2.png";

    switch ($extension) {
        case "png":
        case "jpg":
        case "jpeg":
        case "gif":
            $result = "https://img.icons8.com/dusk/256/000000/image-file.png";
            break;
        case "xcf":
            $result = "https://img.icons8.com/dusk/64/000000/gimp.png";
            break;
        case "pdf":
            $result = "https://img.icons8.com/dusk/256/000000/pdf-2.png";
            break;
        case "wav":
        case "mp3":
        case "flac":
            $result = "https://img.icons8.com/dusk/256/000000/musical.png";
            break;
        case "mp4":
        case "mov":
        case "wmv":
            $result = "https://img.icons8.com/dusk/256/000000/video-file.png";
            break;
        case "zip":
            $result = "https://img.icons8.com/dusk/256/000000/zip.png";
            break;
        case "7zip":
            $result = "https://img.icons8.com/dusk/256/000000/7zip.png";
            break;
        case "rar":
            $result = "https://img.icons8.com/dusk/256/000000/rar.png";
            break;
        case "gz":
        case "tar":
        case "tar.gz":
            $result = "https://img.icons8.com/dusk/256/000000/tar.png";
            break;
        case "txt":
            $result = "https://img.icons8.com/dusk/256/000000/txt.png";
            break;
        case "html":
            $result = "https://img.icons8.com/dusk/256/000000/html-5.png";
            break;
        case "css":
            $result = "https://img.icons8.com/dusk/256/000000/css3.png";
            break;
        case "php":
            $result = "https://img.icons8.com/dusk/256/000000/php-logo.png";
            break;
        case "py":
        case "pyc":
            $result = "https://img.icons8.com/dusk/256/000000/python.png";
            break;
        case "bat":
            $result = "https://img.icons8.com/dusk/256/000000/console.png";
            break;
        case "ps1":
        case "sh":
            $result = "https://img.icons8.com/dusk/256/000000/code.png";
            break;
        case "exe":
            $result = "https://img.icons8.com/dusk/256/000000/windows-logo.png";
            break;
        case "jar":
        case "java":
        case "class":
            $result = "https://img.icons8.com/dusk/256/000000/java-coffee-cup-logo.png";
            break;
        case "c":
            $result = "https://img.icons8.com/dusk/256/000000/c-programming.png";
            break;
        case "cpp":
            $result = "https://img.icons8.com/dusk/256/000000/c-programming.png";
            break;
        case "torrent":
            $result = "https://img.icons8.com/dusk/256/000000/utorrent.png";
            break;
        case "odt":
        case "docx":
            $result = "https://img.icons8.com/dusk/256/000000/ms-word.png";
            break;
        case "ods":
        case "xls":
        case "xlsx":
            $result = "https://img.icons8.com/dusk/256/000000/ms-word.png";
            break;
        case "odp":
        case "pptx":
            $result = "https://img.icons8.com/dusk/256/000000/ms-powerpoint.png";
            break;
        default:
            break;
    }

    return $result;
}


/**
 * Fonction qui crée un evenement pour l'affichage des statistiques administrateur
 *
 * @param string              $type                   - Type d'evenement
 * @param string              $user                   - Nom d'utilisateur associé à l'evenement
 * @param string              $size  (OPT)            - Taille de fichier supprimé/ajouté si evenement associé à un ajout/suppression de fichier
 * @param mysqlconnection     $connection             - Connexion BDD effectuée dans le fichier config-db.php
 *
 * @return void
 */
function addEvent($type, $user, $size, $connection) {

    if ($type == "ACCOUNT_CREATION" || $type == "ACCOUNT_DELETION" || $type == "FILE_UPLOAD" || $type == "FILE_DELETE") {

        $eventsArray = json_decode(getStats('recentEvents', $connection), true);

        while (count($eventsArray) >= 8) {
            array_pop($eventsArray);
        }

        $newEvent = array("type" => $type, "user" => $user, "timestamp" => time(), "badge" => convertUnits($size));
        array_unshift($eventsArray, $newEvent);

        $eventsJSON = json_encode($eventsArray);

        $query = $connection->prepare("UPDATE kioui_stats SET recentEvents = ? WHERE id = 0");
        $query->bind_param("s", $eventsJSON);
        $query->execute();
        $query->close();

    }

}


/**
 * Fonction qui modifie l'activité dans les stats admin
 *
 * @param string              $logtype                - Evenement à log (connexion, inscription, upload ou download)
 * @param mysqlconnection     $connection             - Connexion BDD effectuée dans le fichier config-db.php
 *
 * @return void
 */
function incrementStatLog($logtype, $connection) {

    if ($logtype == "REGISTER" || $logtype == "RECONNECT" || $logtype == "UPLOAD" || $logtype == "DOWNLOAD") {

        $column = "";
        switch ($logtype) {
            case "REGISTER":
                $column = "newUserLog";
                break;
            case "RECONNECT":
                $column = "reconnectUserLog";
                break;
            case "UPLOAD":
                $column = "uploadLog";
                break;
            case "DOWNLOAD":
                $column = "downloadLog";
                break;
        }

        $logs = json_decode(getStats($column, $connection), true);

        $date = date("d/m");

        if (array_key_exists($date, $logs)) {
            //Incrementation
            $logs[$date] += 1;

            $logsJSON = json_encode($logs);

            $query = $connection->prepare("UPDATE kioui_stats SET " . $column . " = ? WHERE id = 0");
            $query->bind_param("s", $logsJSON);
            $query->execute();
            $query->close();
        } else {
            //Ajout jour
            $logsReg = json_decode(getStats('newUserLog', $connection), true);
            $logsReg[$date] = 0;
            $logsRec = json_decode(getStats('reconnectUserLog', $connection), true);
            $logsRec[$date] = 0;
            $logsUp = json_decode(getStats('uploadLog', $connection), true);
            $logsUp[$date] = 0;
            $logsDown = json_decode(getStats('downloadLog', $connection), true);
            $logsDown[$date] = 0;

            $logs[$date] = 1;

            $logsJSON = json_encode($logs);

            $query = $connection->prepare("UPDATE kioui_stats SET newUserLog = ? , reconnectUserLog = ? , uploadLog = ? , downloadLog = ? WHERE id = 0");
            $query->bind_param("ssss", json_encode($logsReg), json_encode($logsRec), json_encode($logsUp), json_encode($logsDown));
            $query->execute();
            $query->close();

            $query = $connection->prepare("UPDATE kioui_stats SET " . $column . " = ? WHERE id = 0");
            $query->bind_param("s", $logsJSON);
            $query->execute();
            $query->close();

        }

    }

}

?>
