'use strict';

(function () {
    function getHelpers() {
        return window.intranetUiHelpers || {};
    }

    function byId(id) {
        return document.getElementById(id);
    }

    function showModal(id) {
        var helpers = getHelpers();
        if (typeof helpers.showModal === 'function') {
            helpers.showModal(id);
            return;
        }

        var modalElement = byId(id);
        if (!modalElement) {
            return;
        }

        if (window.bootstrap && window.bootstrap.Modal) {
            window.bootstrap.Modal.getOrCreateInstance(modalElement).show();
            return;
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
