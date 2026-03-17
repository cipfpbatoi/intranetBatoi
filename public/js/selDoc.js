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

    function hideModal(id) {
        var helpers = getHelpers();
        if (typeof helpers.hideModal === 'function') {
            helpers.hideModal(id);
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

    function handleSeleccionClick(button, event) {
        event.preventDefault();
        setModalAttrs(button, 'A3A');
        openModal('A3A');

        var url = button.getAttribute('data-url') || '';
        var route = button.getAttribute('id') || '';
        var form = document.getElementById('formA3A');
        if (form) {
            form.setAttribute('action', route);
        }

        apiGet(url)
            .then(function (result) {
                if (typeof window.pintaTablaSeleccion === 'function') {
                    window.pintaTablaSeleccion(result.data, '#tableA3');
                }
            })
            .catch(function () {
                console.log('La solicitud no se ha podido completar.');
            });
    }

    function handleSubmitClick(button, event) {
        event.preventDefault();

        var checkAll = document.getElementById('checkall');
        if (checkAll) {
            checkAll.checked = false;
        }

        hideModal('signatura');
        setModalAttrs(button, 'loading');
        openModal('loading');

        var form = document.getElementById('formA3A');
        if (form) {
            form.submit();
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.seleccion').forEach(function (button) {
            button.addEventListener('click', function (event) {
                handleSeleccionClick(button, event);
            });
        });

        document.querySelectorAll('#A3A .submit').forEach(function (button) {
            button.addEventListener('click', function (event) {
                handleSubmitClick(button, event);
            });
        });
    });
})();
