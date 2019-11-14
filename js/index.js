/*
	Fonction qui modifie le modal
 */

function editModalDownload(link) {
	let dlField=document.querySelector("#input-dllink");
	dlField.setAttribute("value", link);
}