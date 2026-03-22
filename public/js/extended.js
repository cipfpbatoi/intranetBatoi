(function () {
    'use strict';

    function getApiAuth() {
        return window.intranetApiAuth || {};
    }

    function getHelpers() {
        return window.intranetUiHelpers || {};
    }

    function setHrefOnly(element) {
        if (element) {
            element.setAttribute('href', '');
        }
    }

    function openModal(id) {
        var helpers = getHelpers();
        if (typeof helpers.showModal === 'function') {
            helpers.showModal(id);
            return;
        }

        var modalElement = document.getElementById(id);
        if (!modalElement) {
            return;
        }

        if (window.bootstrap && window.bootstrap.Modal) {
            window.bootstrap.Modal.getOrCreateInstance(modalElement).show();
            return;
        }
    }

    function fetchInformeOptions(informeCode) {
        var url = '/api/documentacionFCT/' + informeCode;
        var formSeleccion = document.getElementById('formSeleccion');
        if (formSeleccion) {
            formSeleccion.setAttribute('action', url.substring(4));
        }

        var apiAuth = getApiAuth();
        if (typeof apiAuth.apiGet === 'function') {
            return apiAuth.apiGet(url);
        }

        var auth = typeof window.apiAuthOptions === 'function' ? window.apiAuthOptions() : { headers: {}, data: {} };
        var query = new URLSearchParams(auth.data || {}).toString();
        var finalUrl = query ? (url + '?' + query) : url;

        return fetch(finalUrl, {
            method: 'GET',
            headers: auth.headers || {},
            credentials: 'same-origin'
        }).then(function (response) {
            if (!response.ok) {
                throw new Error('HTTP ' + response.status);
            }

            return response.json();
        });
    }

    function updateInformeSelection(informeCode) {
        var informe = document.getElementById('informe');
        if (informe && informe.value !== informeCode) {
            informe.value = informeCode;
        }
    }

    function renderOptions(result) {
        if (typeof window.pintaTablaSeleccion === 'function') {
            window.pintaTablaSeleccion(result.data, '#tableSeleccion');
        }
    }

    function extractInformeCode(button) {
        if (!button) {
            return '';
        }

        var dataUrl = button.getAttribute('data-url') || '';
        if (!dataUrl) {
            return '';
        }

        var parts = dataUrl.split('/');
        return parts[parts.length - 1] || '';
    }

    function handleSeleccionaClick(button, event) {
        event.preventDefault();
        setHrefOnly(button);
        openModal('seleccion');

        var informeCode = extractInformeCode(button) || 'pg0301';
        updateInformeSelection(informeCode);

        fetchInformeOptions(informeCode)
            .then(renderOptions)
            .catch(function () {
                console.log('La solicitud no se ha podido completar.');
            });
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.addEventListener('click', function (event) {
            var button = event.target.closest('.selecciona');
            if (!button) {
                return;
            }

            handleSeleccionaClick(button, event);
        });

        var informe = document.getElementById('informe');
        if (informe) {
            informe.addEventListener('change', function () {
                fetchInformeOptions(informe.value)
                    .then(renderOptions)
                    .catch(function () {
                        console.log('La solicitud no se ha podido completar.');
                    });
            });
        }

        document.querySelectorAll('#seleccion .submit').forEach(function (button) {
            button.addEventListener('click', function (event) {
                event.preventDefault();

                var checkAll = document.getElementById('checkall');
                if (checkAll) {
                    checkAll.checked = false;
                }

                var formSeleccion = document.getElementById('formSeleccion');
                if (formSeleccion) {
                    formSeleccion.submit();
                }
            });
        });
    });
})();
