/**
 * Fonction qui modifie le modal
 */
function editModalShare(link) {
	let shareField = document.querySelector("#input-sharelink");
	shareField.setAttribute("value", link);
}

/**
 * Fonction qui modifie le modal, conserve l'id d'un fichier pour sa suppression
 */
function editModalDelete(fileid) {
	let deleteButton = document.querySelector("#delete-fileid");
	deleteButton.setAttribute("value", fileid);
}

function editModalDirectDownload(path, key, originalName) {
    document.querySelector("#path-directdownload").setAttribute("value", path);
    document.querySelector("#key-directdownload").setAttribute("value", key);
    //document.querySelector("#originalName-directdownload").setAttribute("value", originalName);
    document.querySelector("#originalName-directdownload").innerHTML = originalName;
}


//=============================================================================

// Foncitons pour les cookies <3

function createCookie(name,value,days) {
	if (days) {
		var date = new Date();
		date.setTime(date.getTime()+(days*24*60*60*1000));
		var expires = "; expires="+date.toGMTString();
	}
	else var expires = "";
	document.cookie = name+"="+value+expires+"; path=/";
}

function readCookie(name) {
	var nameEQ = name + "=";
	var ca = document.cookie.split(';');
	for(var i=0;i < ca.length;i++) {
		var c = ca[i];
		while (c.charAt(0)==' ') c = c.substring(1,c.length);
		if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
	}
	return null;
}

function eraseCookie(name) {
	createCookie(name,"",-1);
}

//=============================================================================


/**
 * Fonction qui créer un cookie conservant le thème
 */
function editModalTheme(theme) {
	console.log(theme);
	eraseCookie('theme');
	switch (theme) {
		case 'kioui':
			createCookie('theme', 'kioui', false);
			break;
		case 'frez':
			createCookie('theme', 'frez', false);
			break;
		case 'dark':
			createCookie('theme', 'dark', false);
			break;
	}
	console.log(document.cookie);
	changeTheme();
}


/**
 * Fonction qui modifie la deuxième balise css dans le header
 */
function changeTheme() {

	var link = ".";

	themeCookie = readCookie('theme');
	console.log(themeCookie);

	switch (themeCookie) {
		case 'kioui':
			link = ".";
		break;
		case 'frez':
			link = "./css/theme-frez.css";
		break;
		case 'dark':
			link = "./css/theme-dark.css";
		break;
	}
	
	document.getElementById('theme').href = link ;
}

