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

function fileAdded(e) {
    if (this.files != null) {
        files_compt += this.files.length;
    }

    updateLabelTitle();

    updateFirstLineTab();

    var files_tab = document.querySelector("#files_tab");

    var content_tab = files_tab.innerHTML;

    if (this.files != null) {
        for (var f of this.files) {
            content_tab += fileTabInfos(f);
        }
    }

    files_tab.innerHTML = content_tab;

    // fileName = e.target.value.split('\\').pop();
}

var input = document.querySelector('#inputFile');

var files_compt = 0;

input.addEventListener('change', fileAdded);

fileAdded(null);
