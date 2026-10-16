'use strict';

var id;

document.addEventListener('DOMContentLoaded', function () {
    function submitWithMethod(link) {
        var method = (link.getAttribute('data-method') || 'POST').toUpperCase();
        var confirmation = link.getAttribute('data-confirm');
        var csrf = (document.querySelector('meta[name="csrf-token"]') || {}).content || '';

        if (confirmation && !window.confirm(confirmation)) {
            return;
        }

        var form = document.createElement('form');
        form.method = 'POST';
        form.action = link.href;
        form.hidden = true;

        var token = document.createElement('input');
        token.type = 'hidden';
        token.name = '_token';
        token.value = csrf;
        form.appendChild(token);

        if (method !== 'POST') {
            var methodInput = document.createElement('input');
            methodInput.type = 'hidden';
            methodInput.name = '_method';
            methodInput.value = method;
            form.appendChild(methodInput);
        }

        document.body.appendChild(form);
        form.submit();
    }

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

    document.addEventListener('click', function (event) {
        var methodLink = event.target.closest('#datatable a[data-method]');
        if (!methodLink) {
            return;
        }

        event.preventDefault();
        submitWithMethod(methodLink);
    });

    var formPassword = document.getElementById('formPassword');
    if (formPassword) {
        formPassword.addEventListener('submit', function () {
            this.setAttribute('action', '/reunion/' + id + '/deleteFile');
        });
    }
});
