'use strict';

(function () {
    function byId(id) {
        return document.getElementById(id);
    }

    function showModal(id) {
        var modalElement = byId(id);
        if (!modalElement) {
            return;
        }

        if (window.bootstrap && window.bootstrap.Modal) {
            window.bootstrap.Modal.getOrCreateInstance(modalElement).show();
            return;
        }

        if (window.jQuery) {
            window.jQuery(modalElement).modal('show');
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        var generar = byId('generar');
        var formAviso = byId('formAviso');

        if (generar) {
            generar.addEventListener('click', function (event) {
                event.preventDefault();
                showModal('aviso');
            });
        }

        if (formAviso) {
            formAviso.addEventListener('submit', function () {
                formAviso.action = '/infdepartamento/create';
            });
        }
    });
})();
