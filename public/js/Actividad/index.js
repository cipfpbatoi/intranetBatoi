'use strict';
const MODEL = 'actividad';
var id;

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.refuse').forEach(function (refuseLink) {
        refuseLink.addEventListener('click', function (event) {
            event.preventDefault();
            refuseLink.setAttribute('data-toggle', 'modal');
            refuseLink.setAttribute('data-target', '#dialogo');
            refuseLink.setAttribute('href', '');

            var profileView = refuseLink.closest('.profile_view');
            id = profileView ? profileView.id : undefined;
        });
    });

    var formDialogo = document.getElementById('formDialogo');
    if (formDialogo) {
        formDialogo.addEventListener('submit', function () {
            if (id) {
                formDialogo.setAttribute('action', MODEL + '/' + id + '/refuse');
            }
        });
    }

    var explicacion = document.getElementById('explicacion');
    if (explicacion) {
        explicacion.focus();
    }

});

function getToken() {
	var ppio=document.cookie.indexOf("XSRF-TOKEN=");
	if (ppio==-1)
		return "";
	else
		ppio+=11;	// para no coger el nombre de la cookie
	var fin=document.cookie.indexOf(";",ppio);
	if (fin==-1)
		fin=document.cookie.length;
	return document.cookie.substring(ppio, fin);
}
