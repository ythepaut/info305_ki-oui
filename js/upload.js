function upload() {

}

function fileTabInfos(file) {
    var content = "<tr>";

    content += "<td>" + file.name + "</td>";
    content += "<td>" + file.size + "</td>";

    content += "</tr>";

    return content;
}

function updateFirstLineTab() {
    var first_line_tab = document.querySelector("#first_line_tab");

    first_line_tab.hidden = (files_compt != 0);
}

function updateLabelTitle() {
    var title_tab = document.querySelector("label");

    title_tab.innerHTML = "Ajouter des fichiers (" + files_compt + " sélectionnés)";
}

function recreateTab() {
    var content_tab = "";

    for (var f of all_files) {
        content_tab += fileTabInfos(f);
    }

    return content_tab;
}

function addFiles(files) {
    if (files !== undefined) {
        for (var f of files) {
            all_files.push(f);
        }
    }
}

function fileAdded(e) {
    if (this.files !== undefined) {
        files_compt += this.files.length;
    }

    updateLabelTitle();

    updateFirstLineTab();

    var files_tab = document.querySelector("#files_tab");

    if (all_files.length === 0) {
        basic_files_tab = files_tab.innerHTML;
    }

    addFiles(this.files);

    var content_tab = recreateTab();

    files_tab.innerHTML = basic_files_tab + content_tab;

    // fileName = e.target.value.split('\\').pop();
}

function init() {
    input = document.querySelector('#inputFile');

    files_compt = 0;

    input.addEventListener('change', fileAdded);

    fileAdded(null);
}

var input = undefined;
var files_compt = 0;
var all_files = [];
var basic_files_tab = "";








console.log("Salut, ce message vient de upload.js (dernière ligne) et ne devrait PAS se retrouver ailleurs que sur /ajout pour éviter des soucis (cf. footer.php ligne 64)");
