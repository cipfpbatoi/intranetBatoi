(function () {
    'use strict';

    function getHelpers() {
        return window.intranetUiHelpers || {};
    }

    function trim(value) {
        return (value || '').toString().trim();
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

    function getApiAuthOptions(extraData) {
        if (typeof window.apiAuthOptions === 'function') {
            return window.apiAuthOptions(extraData);
        }

        var legacyTokenEl = document.querySelector('#_token');
        var legacyToken = trim(legacyTokenEl ? legacyTokenEl.textContent : '');
        var bearerMeta = document.querySelector('meta[name="user-bearer-token"]');
        var bearerToken = trim(bearerMeta ? bearerMeta.getAttribute('content') : '');
        var data = extraData ? Object.assign({}, extraData) : {};
        var headers = {};

        if (bearerToken) {
            headers.Authorization = 'Bearer ' + bearerToken;
        }

        if (legacyToken) {
            data.api_token = legacyToken;
        }

        return { headers: headers, data: data };
    }

    function withQueryParams(url, params) {
        var query = new URLSearchParams(params || {}).toString();
        if (!query) {
            return url;
        }

        return url + (url.indexOf('?') === -1 ? '?' : '&') + query;
    }

    function fetchJsonGet(url, auth) {
        return fetch(withQueryParams(url, auth.data), {
            method: 'GET',
            headers: auth.headers,
            credentials: 'same-origin'
        }).then(function (response) {
            if (!response.ok) {
                throw new Error('HTTP ' + response.status);
            }

            return response.json();
        });
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

        var auth = getApiAuthOptions();
        fetchJsonGet(url, auth)
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
