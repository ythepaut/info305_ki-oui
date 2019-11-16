/*
	Fonction qui modifie le modal
 */
function editModalDownload(link) {
	let dlField=document.querySelector("#input-dllink");
	dlField.setAttribute("value", link);
}

/*
	Fonction qui modifie le modal, conserve l'id d'un fichier pour sa suppression
 */
function editModalDelete(fileid) {
	let deleteButton=document.querySelector("#delete-fileid");
	deleteButton.setAttribute("value", fileid);
}