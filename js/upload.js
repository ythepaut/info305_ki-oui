/**
 * Mise à jour de la ligne "Aucun fichier sélectionné"
 */
function updateFirstLineTab() {
    if (all_files.length !== 0) {
        var first_line_tab = document.querySelector("#first_line_tab");
        first_line_tab.setAttribute("hidden", "1");
    }
}

/**
 * Mise à jour du label "Ajouter des fichiers ({n} sélectionnés)"
 */
function updateLabelTitle() {
    var nb = 0;

    for (var file of all_files) {
        if (file != null) {
            nb ++;
        }
    }

    var title_tab = document.querySelector("#inputLabel");

    if (nb == 0) {
        title_tab.innerHTML = "Ajouter des fichiers";
    }
    else {
        title_tab.innerHTML = "Ajouter des fichiers (" + all_files.length + " en cours d'ajout)";
    }
}

/**
 * Permet de rentre une taille en octet plus lisible, avec une unité
 *
 * @param   {int}       size            Taille à transformer (ex : 12345)
 *
 * @return  {string}    size_str        Taille (ex : "12.3 ko")
 */
function transformSize(size) {
    var n = 0;

    while (size >= 1000) {
        size = size/1000;
        size = Math.floor(size*10)/10;
        n ++;
    }

    var unit;

    switch (n) {
        case 0:
            unit = "o";
            break;

        case 1:
            unit = "ko";
            break;

        case 2:
            unit = "Mo";
            break;

        case 3:
            unit = "Go";
            break;

        default:
            unit = "(?)";
            break;
    }

    var size_str = size + " " + unit;

    return size_str;
}

/**
 * Ajoute une ligne dans le tableau des fichiers
 *
 * @param   {file}      file            Nouveaux fichier
 * @param   {int}       n_file          Numéro du fichier
 */
function createTabBar(file, n_file) {
    var row = files_tab.insertRow(-1);

    var name_cell = row.insertCell(0);
    var size_cell = row.insertCell(1);
    var progress_cell = row.insertCell(2);

    name_cell.innerHTML = file.name;

    var size_str = transformSize(file.size);

    size_cell.innerHTML = size_str;

    var progress_bar_container = document.createElement("div");
    progress_bar_container.classList.add("progress");

    var progress_bar = document.createElement("div");
    progress_bar.setAttribute("id", "file-progress-bar-N" + n_file)
    progress_bar.setAttribute("class", "progress-bar progress-bar-striped progress-bar-animated");
    progress_bar.setAttribute("role", "progressbar");
    progress_bar.setAttribute("aria-valuenow", "0");
    progress_bar.setAttribute("aria-valuemin", "0");
    progress_bar.setAttribute("aria-valuemax", "100");
    progress_bar.setAttribute("style", "width:0%");
    progress_bar.innerHTML = "0 %";

    progress_bar_container.appendChild(progress_bar);
    progress_cell.appendChild(progress_bar_container);
}

/**
 * Ajoute des fichiers et les envoie un par un
 *
 * @param   {files[]}   files           Fichiers à ajouter
 */
function addFiles(files) {
    for (var file of files) {
        if (file.size < allowedSpace) {
            allowedSpace -= file.size;

            let n_file = all_files.length;

            all_files.push(file);

            createTabBar(file, n_file);
            updateLabelTitle();
            updateFirstLineTab();

            sendFile(file, n_file);
        }
        else {
            $('#modalUploadFileError').modal();
            return;
        }
    }
}

/**
 * Envoie un fichier
 *
 * @param   {file}      file            Fichier à envoyer
 * @param   {int}       n_file          Numéro du fichier
 */
function sendFile(file, n_file) {
    var tps = Math.random()*500 + 750;

    setTimeout(function() {
        var location = document.querySelector("#uploadForm").getAttribute("action");

        var formData = new FormData();

        formData.append("files[]", file);
        formData.append("action", "upload-file");

        sendRequest(formData, location, n_file);
    }, tps);
}

/**
 * Envoie une requête POST
 *
 * @param   {FormData}  formData Infos à envoyer
 * @param   {string}    url      Adresse, où envoyer la requête (actions.php)
 * @param   {int}       n_file   Numéro du fichier
 */
function sendRequest(formData, url, n_file) {
  var xhr = new XMLHttpRequest();

  xhr.upload.addEventListener('loadstart', function(e) {onloadstartHandler(e, n_file);}, false);
  xhr.upload.addEventListener('progress', function(e) {onprogressHandler(e, n_file);}, false);
  xhr.upload.addEventListener('load', function(e) {onloadHandler(e, n_file);}, false);
  xhr.addEventListener('readystatechange', function(e) {onreadystatechangeHandler(e, n_file);}, false);

  xhr.open('POST', url, true);

  xhr.send(formData);
}

/**
 * Début de transmission
 */
function onloadstartHandler(e, n_file) {
    console.log("start file #" + n_file);
}

/**
 * Fin de transmission
 */
function onloadHandler(e, n_file) {
    console.log("uploaded file #" + n_file);

    var progress_bar = document.querySelector("#file-progress-bar-N" + n_file);
    progress_bar.setAttribute("style", "width:100%");

    var size = all_files[n_file].size;

    all_files[n_file] = null;

    var tps = size / (100*10**6) * 6;

    if (tps < 1) {
        tps = 1;
    }

    tps = (Math.random()*50 + 75)/100 * tps;

    progress_bar.classList.add("bg-info");
    progress_bar.innerHTML = "Chiffrement...";

    setTimeout(function() {
        progress_bar.classList.remove("bg-info");
        progress_bar.classList.add("bg-success");
        progress_bar.innerHTML = "Terminé";
    }, tps*1000);

    updateLabelTitle();

    for (var file of all_files) {
        if (file != null) {
            return;
        }
    }

    setTimeout(function() {
        for (var file of all_files) {
            if (file != null) {
                return;
            }
        }

        location.reload();
    }, 1000);
}

/**
 * Progression
 */
function onprogressHandler(e, n_file) {
    var percent = Math.floor(e.loaded/e.total*100*10)/10;

    console.log("Progression :");
    console.log(percent);
    console.log("---");

    var progress_bar = document.querySelector("#file-progress-bar-N" + n_file);
    progress_bar.setAttribute("style", "width:" + percent + "%");
    progress_bar.innerHTML = percent + " %";
}

/**
 * Réponse du serveur
 */
function onreadystatechangeHandler(e, n_file) {
    var readyState = e.target.readyState;
    var text = e.target.responseText;
    var status = e.target.status;

    console.log("Réponse :");
    console.log(readyState);
    console.log(status);
    console.log("---");

    // TODO
    /*
    if (readyState == 4 && status == '200') {
        if (e.target.responseText) {
            console.log(e.target.responseText);
            var result = document.querySelector("#response");
            result.innerHTML = '<p>The server saw it as:</p><pre>' + e.target.responseText + '</pre>';
        }
    }
    else {
        var progress_bar = document.querySelector("#file-progress-bar-N" + n_file);
        progress_bar.classList.remove("bg-info");
        progress_bar.classList.remove("bg-success");
        progress_bar.classList.add("bg-danger");

        console.log("File #" + n_file + " errored");
        console.log(readyState);
        console.log(status);
        console.log(e.target.responseText);
    }*/
}

var all_files = [];
// Tous les fichiers

var dropzone = document.querySelector(".dropzone");

if (dropzone !== null) {
    dropzone.ondrop = function(e) {
        this.classList.remove("dragover")
        e.preventDefault();
        addFiles(e.dataTransfer.files);
    };

    dropzone.ondragover = function() {
        this.classList.add("dragover")
        return false;
    };

    dropzone.ondragleave = function() {
        this.classList.remove("dragover")
        return false;
    };
}

var allowedSpace = null;

var allowedSpace_document = document.querySelector("#allowedSpace");

if (allowedSpace_document !== null) {
    allowedSpace = parseInt(allowedSpace_document.getAttribute("value"));

    updateLabelTitle();
    updateFirstLineTab();
}
