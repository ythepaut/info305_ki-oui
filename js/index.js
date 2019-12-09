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

/**
 * Je ferai le commentaire <3 -Roro
 */
function editModalDeleteMultipleFiles() {
    let deletedCheckbox_tab_all = document.querySelectorAll(".checkbox-delete-files");

    var deletedCheckbox_tab = [];

    deletedCheckbox_tab_all.forEach(function(elem) {
        if (elem.checked) {
            deletedCheckbox_tab.push(elem);
        }
    });

    let spanHowManyFilesSelected = document.querySelector("#howManyFilesSelected");

    if (deletedCheckbox_tab.length === 0) {
        spanHowManyFilesSelected.innerHTML = "Aucun fichier sélectionné";
    }
    else {
        if (deletedCheckbox_tab.length === 1) {
            spanHowManyFilesSelected.innerHTML = "Êtes-vous sûr de vouloir supprimer ce fichier ?";
        }
        else {
            spanHowManyFilesSelected.innerHTML = "Êtes-vous sûr de vouloir supprimer ces " + deletedCheckbox_tab.length + " fichiers ?";
        }

        var tab_fileSelectedForDeletion = document.querySelector("#fileSelectedForDeletion");
        var div_idToDelete = document.querySelector("#idToDelete");

        deletedCheckbox_tab.forEach(function(elem) {
            var file_info_td = document.createElement("td");
            file_info_td.setAttribute("class", "d-none d-lg-table-cell");
            file_info_td.innerHTML = elem.getAttribute("name");

            tab_fileSelectedForDeletion.appendChild(file_info_td);

            var file_id_input = document.createElement("input");
            file_id_input.setAttribute("type", "hidden");
            file_id_input.setAttribute("class", "form-control");
            file_id_input.setAttribute("name", "delete-fileid[]");
            file_id_input.setAttribute("value", elem.getAttribute("value"));

            div_idToDelete.appendChild(file_id_input);
        });
    }
}

function editModalDirectDownload(path, key, originalName) {
    document.querySelector("#path-directdownload").setAttribute("value", path);
    document.querySelector("#key-directdownload").setAttribute("value", key);
    //document.querySelector("#originalName-directdownload").setAttribute("value", originalName);
    document.querySelector("#originalName-directdownload").innerHTML = originalName;
}

/**
 * Fonction qui édite le modal de modification de niveau d'accès
*/
function editModalAccessLevel(id){
	document.querySelector("#change-access-level_iduser").setAttribute("value", id);
}

/**
 * Fonction qui édite le modal de modification du quota
*/
function editModalQuota(id){
	document.querySelector("#change-quota_iduser").setAttribute("value", id);
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
	themeCookie = readCookie('theme');
	changeTheme(themeCookie);
}


/**
 * Fonction qui change le theme en fonciton des cookies
 */
function changeTheme(themeCookie) {

    var html = document.getElementsByTagName('html')[0];
    var background = document.getElementById("index-background");
    var body = document.getElementsByTagName('body')[0];

	switch (themeCookie) {

		case 'kioui':
			html.style.cssText = "--main-color: #10ac84;";
			html.style.cssText += "--main-light-color: #1dd1a1;";
			html.style.cssText += "--main-light-color-lighter: #5adfbb;";
			html.style.cssText += "--background-color: #f1f1f1;";
			html.style.cssText += "--background-light-color: #fff;";
			// image d'accueil
			if (background) {	
			background.style.setProperty("background-image", 'url("../ressources/img/accueil.jpeg")');
			}
		break;

		case 'frez':
			html.style.cssText = "--main-color: #ee5253;";
			html.style.cssText += "--main-light-color: #ff6b6b;";
			html.style.cssText += "--main-light-color-lighter: #F38283;";
			html.style.cssText += "--background-color: #f1f1f1;";
			html.style.cssText += "--background-light-color: #fff;";
			// image d'accueil
			if (background) {	
			background.style.setProperty("background-image", 'url("../ressources/img/accueil-frez.jpeg")');
			}
		break;

		case 'dark':
			html.style.cssText = "--main-color: black;";
			html.style.cssText += "--main-light-color: white;";
			html.style.cssText += "--main-light-color-lighter: #5adfbb;";
			html.style.cssText += "--background-color: #1A1A1A;";
			html.style.cssText += "--background-light-color: #212121;";
			body.style.setProperty("color", "white !veryimportant;");
			body.style.setProperty("background-color", "var(--background-light-color) !veryimportant;");
			// image d'accueil
			if (background) {	
			background.style.setProperty("background-image", 'url("../ressources/img/accueil-dark.jpeg")');
			}
		break;
	}
}
// Exécution de la fonciton ci-dessus
themeCookie = readCookie('theme');
changeTheme(themeCookie);


/**
 * Fonction qui renvoie le chemin relatif à la page à partie de la source relative
 * (copie de la fonction getSrc dans util.php)
 * Exemple :
 * relative_src = "/css/style.css"
 * Si nous sommes dans l'espace utilisateur, on aura
 * result_src = "../../css/style.css"
 */
function getSrcJs(relative_src) {

	let current_path = window.location.pathname+window.location.search;
	let result_src = relative_src;

	for (var i=0 ; i < current_path.length ; i++){
		if (current_path.charAt(i)=='/'){
			result_src = '../' + result_src;
		}
	}
	return result_src;
}
