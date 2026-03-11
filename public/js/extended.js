(function () {
    'use strict';

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

        if (window.jQuery) {
            window.jQuery(modalElement).modal('show');
        }
    }

    function fetchInformeOptions(informeCode) {
        var url = '/api/documentacionFCT/' + informeCode;
        var formSeleccion = document.getElementById('formSeleccion');
        if (formSeleccion) {
            formSeleccion.setAttribute('action', url.substring(4));
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

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.selecciona').forEach(function (button) {
            button.addEventListener('click', function (event) {
                event.preventDefault();
                setHrefOnly(button);
                openModal('seleccion');

                var formSeleccion = document.getElementById('formSeleccion');
                var defaultUrl = '/api/documentacionFCT/pg0301';
                if (formSeleccion) {
                    formSeleccion.setAttribute('action', defaultUrl.substring(4));
                }
            });
        });

        var informe = document.getElementById('informe');
        if (informe) {
            informe.addEventListener('change', function () {
                fetchInformeOptions(informe.value)
                    .then(function (result) {
                        if (typeof window.pintaTablaSeleccion === 'function') {
                            window.pintaTablaSeleccion(result.data, '#tableSeleccion');
                        }
                    })
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
