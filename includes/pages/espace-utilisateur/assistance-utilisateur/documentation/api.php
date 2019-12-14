<h4 class="panel-title">API publique de KI-OUI</h4>

<p>Les differentes fonctionnalités de l'API sont décrites ci-après.<br />
Les appels se font par la methode POST ou GET à l'URL <code>https://ki-oui.com/api/</code>. Les paramètres d'entrée/sortie sont également précisés.<br />
Les réponses sont retournées au format JSON, et contiennent au moins la clé "status" qui vaut "success", "warning" ou "error".
<!--<br />
Voici quelques exemples de reponse :<br />
<br />
<kbd style="padding: 0.6em;">Succès : { "status": "success", "message": "Your file has been uploaded." }</kbd><br /><br />
<kbd style="padding: 0.6em;">Erreur : { "status": "error", "error": "ERROR_EXPIRED_TOKEN", "verbose": "Token is expired." }</kbd><br />-->

</p>

<br /><hr /><br />

<div>
    <h5><b>AUTHENTIFICATION</b></h5>

    <p>Permet d'acquérir un jeton d'accès et la clé d'un compte utilisateur, nécessaires pour la plupart des reqûetes API.</p>
    <p><b>Paramètres :</b><br />
        &nbsp;&nbsp;&nbsp;&nbsp;action = "auth"<br />
        &nbsp;&nbsp;&nbsp;&nbsp;email : Adresse e-mail du compte<br />
        &nbsp;&nbsp;&nbsp;&nbsp;passwd : Mot de passe du compte<br />
        &nbsp;&nbsp;&nbsp;&nbsp;duration : Durée de validité du jeton d'accès (en secondes)<br />
    </p>
    <p><b>Valeurs de retour (en cas de succès) :</b><br />
        &nbsp;&nbsp;&nbsp;&nbsp;status : Information sur la requête (succès, erreur)<br />
        &nbsp;&nbsp;&nbsp;&nbsp;message : Message d'information sur la requête<br />
        &nbsp;&nbsp;&nbsp;&nbsp;token : Jeton d'accès de l'utilisateur<br />
        &nbsp;&nbsp;&nbsp;&nbsp;key : Clé de l'utilisateur<br />
    </p>
    <p><b>Exemple de requête GET :</b><br /><br />
        &nbsp;&nbsp;&nbsp;&nbsp;<kbd style="padding: 0.6em;">GET https://ki-oui.com/api/?action=auth&email=test@example.com&passwd=Kd4j1Aydp1l&duration=3600</kbd><br />
    </p>
</div>

<br /><hr /><br />

<div>
    <h5><b>TÉLÉVERSER UN FICHIER</b></h5>

    <p>Permet d'ajouter un fichier sur un compte.</p>
    <p><b>Paramètres :</b><br />
        &nbsp;&nbsp;&nbsp;&nbsp;action = "upload"<br />
        &nbsp;&nbsp;&nbsp;&nbsp;token : Jeton d'accès de l'utilisateur<br />
        &nbsp;&nbsp;&nbsp;&nbsp;key : Clé de l'utilisateur<br />
        &nbsp;&nbsp;&nbsp;&nbsp;filename : Nom du fichier<br />
        &nbsp;&nbsp;&nbsp;&nbsp;data : Données du fichier à téléverser<br />
    </p>
    <p><b>Valeurs de retour (en cas de succès) :</b><br />
        &nbsp;&nbsp;&nbsp;&nbsp;status : Information sur la requête (succès, erreur)<br />
        &nbsp;&nbsp;&nbsp;&nbsp;message : Message d'information sur la requête<br />
    </p>
    <p><b>Exemple de requête GET :</b><br /><br />
        &nbsp;&nbsp;&nbsp;&nbsp;<kbd style="padding: 0.6em;">GET https://ki-oui.com/api/?action=upload&token={TOKEN}&key={KEY}&filename=test.txt&data=test</kbd><br />
    </p>
</div>

<br /><hr /><br />

<div>
    <h5><b>ACQUISITION DES STATISTIQUES D'UN FICHIER</b></h5>

    <p>Permet d'acquérir des statistiques sur un fichier.</p>
    <p><b>Paramètres :</b><br />
        &nbsp;&nbsp;&nbsp;&nbsp;action = "getfilestats"<br />
        &nbsp;&nbsp;&nbsp;&nbsp;fileid : Nom unique du fichier<br />
    </p>
    <p><b>Valeurs de retour (en cas de succès) :</b><br />
        &nbsp;&nbsp;&nbsp;&nbsp;status : Information sur la requête (succès, erreur)<br />
        &nbsp;&nbsp;&nbsp;&nbsp;message : Message d'information sur la requête<br />
        &nbsp;&nbsp;&nbsp;&nbsp;downloads : Nombre de téléchargements du fichier<br />
        &nbsp;&nbsp;&nbsp;&nbsp;uploadDate : Date d'envoie du fichier (format Unix)<br />
        &nbsp;&nbsp;&nbsp;&nbsp;size : Taille du fichier (octets)<br />
        &nbsp;&nbsp;&nbsp;&nbsp;hash : Hash du fichier<br />
    </p>
    <p><b>Exemple de requête GET :</b><br /><br />
        &nbsp;&nbsp;&nbsp;&nbsp;<kbd style="padding: 0.6em;">GET https://ki-oui.com/api/?action=getfilestats&fileid=Of5gm1sktLk67lQf</kbd><br />
    </p>
</div>

<br /><hr /><br />

<div>
    <h5><b>VÉRIFICATION DE LA CLÉ D'UN FICHIER</b></h5>

    <p>Permet de vérifier si la clé de chiffrement d'un fichier est correct.</p>
    <p><b>Paramètres :</b><br />
        &nbsp;&nbsp;&nbsp;&nbsp;action = "isvalidkey"<br />
        &nbsp;&nbsp;&nbsp;&nbsp;fileid : Nom unique du fichier<br />
        &nbsp;&nbsp;&nbsp;&nbsp;filekey : Clé de chiffrement du fichier<br />
    </p>
    <p><b>Valeurs de retour (en cas de succès) :</b><br />
        &nbsp;&nbsp;&nbsp;&nbsp;status : Information sur la requête (succès, erreur)<br />
        &nbsp;&nbsp;&nbsp;&nbsp;message : Message d'information sur la requête<br />
        &nbsp;&nbsp;&nbsp;&nbsp;valid : "true" ou "false" selon la validité de la clé<br />
    </p>
    <p><b>Exemple de requête GET :</b><br /><br />
        &nbsp;&nbsp;&nbsp;&nbsp;<kbd style="padding: 0.6em;">GET https://ki-oui.com/api/?action=isvalidkey&fileid=Of5gm1sktLk67lQf&filekey=0123456789abcdef</kbd><br />
    </p>
</div>
