'use strict';

var id;

document.addEventListener('DOMContentLoaded', function () {
    function showPasswordModal() {
        if (window.intranetUiHelpers && typeof window.intranetUiHelpers.showModal === 'function') {
            window.intranetUiHelpers.showModal('password');
            return;
        }

        var modalElement = document.getElementById('password');
        if (modalElement && window.bootstrap && window.bootstrap.Modal) {
            window.bootstrap.Modal.getOrCreateInstance(modalElement).show();
            return;
        }

        if (modalElement && window.jQuery) {
            window.jQuery(modalElement).modal('show');
        }
    }

    document.addEventListener('click', function (event) {
        var clickedElement = event.target.closest('#datatable [id^="deleteFile"], #datatable .fa-unlock');
        if (!clickedElement) {
            return;
        }

        event.preventDefault();
        var unlockButton = clickedElement.closest('a') || clickedElement;
        var row = unlockButton.closest('.lineaGrupo') || unlockButton.closest('tr');
        id = row ? row.id : '';
        showPasswordModal();
    });

    var formPassword = document.getElementById('formPassword');
    if (formPassword) {
        formPassword.addEventListener('submit', function () {
            this.setAttribute('action', '/reunion/' + id + '/deleteFile');
        });
    }
});
