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
 * Configure l'input suivante
 */
function setNewInput() {
    var old_input = document.querySelector('#inputFile');

    if (old_input !== null) {
        old_input.setAttribute("id", "inputFile_old");
        old_input.setAttribute("hidden", "1");
        old_input.removeEventListener("change", fileAdded);
        // On cache l'input précédent, on marque son id et on retire la fonction à exécuter lors de
        // l'ajout de fichier afin de laisser cet input tel quel
    }

    var current_input = document.createElement("input");
    current_input.setAttribute("type", "file");
    current_input.setAttribute("name", "files[]");
    current_input.setAttribute("id", "inputFile");
    current_input.setAttribute("multiple", "1");
    current_input.addEventListener("change", fileAdded);
    // On crée une nouvelle séléction de fichiers

    var div_files_select = document.querySelector("#allInputs");
    div_files_select.appendChild(current_input);
    // On ajoute cette sélection au div contenant les autres sélections
}

/**
 * Supprime l'input courante si elle est invalide
 */
function resetInput() {
    var current_input = document.querySelector('#inputFile');

    if (current_input !== null) {
        current_input.setAttribute("disabled", "1");
    }
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

        name_cell.innerHTML = file.name;

        var size_str = transformSize(file.size);

        size_cell.innerHTML = size_str;
    }
}

/**
 * Fonction exécutée lors de l'ajout d'un fichier
 *
 * @param  {event}  e           Événement d'ajout
 */
function fileAdded(e) {
    for (var file of this.files) {
        all_files.push(file);
    }

    updateTab(this.files);
    setNewInput();
    updateLabelTitle();
    updateFirstLineTab();

    var size = 0;

    for (let file of all_files) {
        size += file.size;
    }

    if (size > MAX_SIZE) {
        $('#modalUploadFileError').modal();
    }
}

/**
 * Initialisation de l'ajout de fichiers
 */
function init() {
    setNewInput();
    updateLabelTitle();
    updateFirstLineTab();
}

function sendFiles() {
    document.querySelector("#boutonEnvoi").setAttribute("disabled", "true");

    document.querySelector("#envoyer").style.display = "none";
    document.querySelector("#envoi").style.display = "block";

    setTimeout(function() {
        var form = document.querySelector("#uploadForm");

        var size = 0;

        for (let file of all_files) {
            size += file.size;
        }

        console.log(size);

        if (size > MAX_SIZE) {
            $('#modalUploadFileError').modal();
        }
        else {
            form.submit();
        }
    }, 1000);
}

var all_files = [];
// Tous les fichiers

var MAX_SIZE = 200 * Math.pow(10, 6);
// Qu'on ne peut pas mettre en const car le script est importé 2 fois lol
