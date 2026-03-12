'use strict';

var id;
var MODEL = 'falta';

document.addEventListener('DOMContentLoaded', function () {
    document.addEventListener('click', function (event) {
        var refuseButton = event.target.closest('.refuse');
        if (refuseButton) {
            event.preventDefault();
            refuseButton.setAttribute('data-toggle', 'modal');
            refuseButton.setAttribute('data-target', '#dialogo');
            refuseButton.setAttribute('href', '');
            var profileView = refuseButton.closest('.profile_view');
            id = profileView ? profileView.id : '';
            return;
        }

        var convalidacionButton = event.target.closest('.convalidacion');
        if (convalidacionButton) {
            event.preventDefault();
            convalidacionButton.setAttribute('data-toggle', 'modal');
            convalidacionButton.setAttribute('data-target', '#password');
            convalidacionButton.setAttribute('href', '');
            return;
        }

        var passwordSubmitButton = event.target.closest('#password .submit');
        if (passwordSubmitButton) {
            event.preventDefault();
            if (window.intranetUiHelpers) {
                window.intranetUiHelpers.hideModal('password');
                window.intranetUiHelpers.showModal('loading');
            }
            var formPassword = document.getElementById('formPassword');
            if (formPassword) {
                formPassword.setAttribute('action', '/direccion/itaca/faltes');
                formPassword.submit();
            }
            passwordSubmitButton.setAttribute('data-toggle', 'modal');
            passwordSubmitButton.setAttribute('data-target', '#loading');
            passwordSubmitButton.setAttribute('href', '');
        }
    });

    var formDialogo = document.getElementById('formDialogo');
    if (formDialogo) {
        formDialogo.addEventListener('submit', function () {
            this.setAttribute('action', MODEL + '/' + id + '/refuse');
        });
    }

    var dialogo = document.getElementById('dialogo');
    if (dialogo) {
        dialogo.focus();
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
