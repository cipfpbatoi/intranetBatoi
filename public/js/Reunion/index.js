'use strict';

var id;

document.addEventListener('DOMContentLoaded', function () {
    document.addEventListener('click', function (event) {
        var unlockButton = event.target.closest('#datatable .fa-unlock');
        if (!unlockButton) {
            return;
        }

        event.preventDefault();
        var row = unlockButton.closest('.lineaGrupo') || unlockButton.closest('tr');
        id = row ? row.id : '';
        unlockButton.setAttribute('data-toggle', 'modal');
        unlockButton.setAttribute('data-target', '#password');
        unlockButton.setAttribute('href', '');
    });

    var formPassword = document.getElementById('formPassword');
    if (formPassword) {
        formPassword.addEventListener('submit', function () {
            this.setAttribute('action', '/reunion/' + id + '/deleteFile');
        });
    }
});
