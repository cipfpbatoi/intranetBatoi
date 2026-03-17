(function () {
    'use strict';

    function getHelpers() {
        return window.intranetUiHelpers || {};
    }

    function getApiAuth() {
        return window.intranetApiAuth || {};
    }

    function setModalAttrs(element, targetId) {
        if (!element) {
            return;
        }

        element.setAttribute('href', '');
    }

    function openModal(id) {
        var helpers = getHelpers();
        if (typeof helpers.showModal === 'function') {
            helpers.showModal(id);
            return;
        }
    }

    function apiGet(url) {
        var apiAuth = getApiAuth();
        if (typeof apiAuth.apiGet === 'function') {
            return apiAuth.apiGet(url);
        }

        return Promise.reject(new Error('intranetApiAuth.apiGet no disponible'));
    }

    function handleSeleccionaClick(button, event) {
        event.preventDefault();
        setModalAttrs(button, 'seleccion');
        openModal('seleccion');

        var url = button.getAttribute('data-url') || '';
        var formSeleccion = document.getElementById('formSeleccion');
        if (formSeleccion && url.length >= 4) {
            formSeleccion.setAttribute('action', url.substring(4));
        }

        apiGet(url)
            .then(function (result) {
                if (typeof window.pintaTablaSeleccion === 'function') {
                    window.pintaTablaSeleccion(result.data, '#tableSeleccion');
                }
            })
            .catch(function () {
                console.log('La solicitud no se ha podido completar.');
            });
    }

    function handleSubmitClick(event) {
        event.preventDefault();

        var checkAll = document.getElementById('checkall');
        if (checkAll) {
            checkAll.checked = false;
        }

        var formSeleccion = document.getElementById('formSeleccion');
        if (formSeleccion) {
            formSeleccion.submit();
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.selecciona').forEach(function (button) {
            button.addEventListener('click', function (event) {
                handleSeleccionaClick(button, event);
            });
        });

        document.querySelectorAll('#seleccion .submit').forEach(function (button) {
            button.addEventListener('click', handleSubmitClick);
        });
    });
})();
