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
    var title_tab = document.querySelector("#inputLabel");
    title_tab.innerHTML = "Ajouter des fichiers (" + all_files.length + " sélectionnés)";
}

/**
 * Permet de rentre une taille en octet plus lisible, avec une unité
 *
 * @param  {int}    size        Taille à transformer (ex : 12345)
 *
 * @return {string} size_str    Taille (ex : "12.3 ko")
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
 * Met à jour le tableau des fichiers
 *
 * @param  {file[]}  files      Tableau des nouveaux fichiers
 */
function updateTab(files) {
    for (var file of files) {
        var row = files_tab.insertRow(-1);

        var name_cell = row.insertCell(0);
        var size_cell = row.insertCell(1);
        var progress_cell = row.insertCell(2);

        name_cell.innerHTML = file.name;

        var size_str = transformSize(file.size);

        size_cell.innerHTML = size_str;

        var progress_bar = document.createElement("div");
        progress_bar.setAttribute("class", "progress-bar progress-bar-striped progress-bar-animated");
        progress_bar.setAttribute("role", "progressbar");
        progress_bar.setAttribute("aria-valuenow", "0");
        progress_bar.setAttribute("aria-valuemin", "0");
        progress_bar.setAttribute("aria-valuemax", "100");
        progress_bar.setAttribute("style", "width:100%");
        progress_bar.innerHTML = "TODO %";

        progress_cell.appendChild(progress_bar);
    }
}

/**
 * Fonction exécutée lors de l'ajout d'un fichier
 *
 * @param  {event}  e           Événement d'ajout
 */
function fileAdded(e) {
    console.log("fileAdded");

    addFiles(this.files);
}

function addFiles(files) {
    for (var file of files) {
        all_files.push(file);
    }

    console.log("-----");
    console.log(files);
    console.log(all_files);
    console.log("-----");

    updateTab(files);
    updateLabelTitle();
    updateFirstLineTab();

    var size = 0;

    for (let file of all_files) {
        size += file.size;
    }

    if (size < allowedSpace) {
        allowedSpace -= size;
        sendFiles();
    }
    else {
        $('#modalUploadFileError').modal();
    }
}

function sendFiles() {
    setTimeout(function() {
        var location = document.querySelector("#uploadForm").getAttribute("action");

        for (var i=0; i<all_files.length; i++) {
            let item = all_files[i];
            formData.append('files[]', item);
        }

        formData.append("action", "upload-file");

        console.log("envoyé 1 ? sendFiles");
        sendXHRequest(formData, location);
        console.log("envoyé 2 ? sendFiles");

        all_files = [];
    }, 1000);
}

function traverseFileTree(item, path) {
    console.log("???");
    //!
    return false;


    path = path || "";
    if (item.isFile) {
        item.file(function(file) {
            console.log(file);                  // show info
            formData.append('file', file);    // file exist, but don't append
        });

    } else if (item.isDirectory) {
        var dirReader = item.createReader();
        dirReader.readEntries(function(entries) {
            for (var i=0; i<entries.length; i++) {
                traverseFileTree(entries[i], path + item.name + "/");
            }
        });
    }
    else {
        console.log(item);
    }
}


var all_files = [];
// Tous les fichiers

var formData = new FormData();

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

input_file = document.querySelector("#inputFile");

if (input_file !== null) {
    input_file.addEventListener("change", fileAdded);
}






















// Once the FormData instance is ready and we know
// where to send the data, the code is the same
// for both variants of this technique
function sendXHRequest(formData, uri) {
  // Get an XMLHttpRequest instance
  var xhr = new XMLHttpRequest();
  // Set up events
  xhr.upload.addEventListener('loadstart', onloadstartHandler, false);
  xhr.upload.addEventListener('progress', onprogressHandler, false);
  xhr.upload.addEventListener('load', onloadHandler, false);
  xhr.addEventListener('readystatechange', onreadystatechangeHandler, false);
  // Set up request
  xhr.open('POST', uri, true);
  // Fire!
  xhr.send(formData);
}
// Handle the start of the transmission
function onloadstartHandler(evt) {
    console.log("start");
  var div = document.getElementById('upload-status');
  div.innerHTML = 'Upload started.';
}
// Handle the end of the transmission
function onloadHandler(evt) {
    console.log("uploaded");
  var div = document.getElementById('upload-status');
  div.innerHTML += '<' + 'br>File uploaded. Waiting for response.';
}
// Handle the progress
function onprogressHandler(evt) {
    var percent = evt.loaded/evt.total*100;
    console.log(percent + " %");
  var div = document.getElementById('progress');
  div.innerHTML = 'Progress: ' + percent + '%';
}
// Handle the response from the server
function onreadystatechangeHandler(evt) {
    console.log("attente");
  var status, text, readyState;
  try {
    readyState = evt.target.readyState;
    text = evt.target.responseText;
    status = evt.target.status;
  }
  catch(e) {
    return;
  }
  if (readyState == 4 && status == '200' && evt.target.responseText) {
      console.log("ok");
    var status = document.getElementById('upload-status');
    status.innerHTML += '<' + 'br>Success!';
    var result = document.getElementById('result');
    // result.innerHTML = '<p>The server saw it as:</p><pre>' + evt.target.responseText + '</pre>';
  }
  else {
      console.log("ERROR");
      console.log(readyState);
      console.log(status);
      console.log(evt.target.responseText);
  }
}
