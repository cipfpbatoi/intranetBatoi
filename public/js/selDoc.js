(function () {
    'use strict';

    function getHelpers() {
        return window.intranetUiHelpers || {};
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
    }

    function hideModal(id) {
        var helpers = getHelpers();
        if (typeof helpers.hideModal === 'function') {
            helpers.hideModal(id);
            return;
        }

        var modalElement = document.getElementById(id);
        if (!modalElement) {
            return;
        }

        if (window.bootstrap && window.bootstrap.Modal) {
            window.bootstrap.Modal.getOrCreateInstance(modalElement).hide();
            return;
        }
    }

    function getApiAuthOptions(extraData) {
        if (typeof window.apiAuthOptions === 'function') {
            return window.apiAuthOptions(extraData);
        }

        var bearerMeta = document.querySelector('meta[name="user-bearer-token"]');
        var bearerToken = (bearerMeta ? bearerMeta.getAttribute('content') : '') || '';
        var csrfMeta = document.querySelector('meta[name="csrf-token"]');
        var csrfToken = (csrfMeta ? csrfMeta.getAttribute('content') : '') || '';
        var data = extraData ? Object.assign({}, extraData) : {};
        var headers = {};

        if (csrfToken.trim()) {
            headers['X-CSRF-TOKEN'] = csrfToken.trim();
        }

        if (bearerToken.trim()) {
            headers.Authorization = 'Bearer ' + bearerToken.trim();
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

    function fetchJson(url, auth) {
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

        var auth = getApiAuthOptions();

        fetchJson(url, auth)
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
