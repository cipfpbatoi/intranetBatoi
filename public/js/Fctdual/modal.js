'use strict';

/**
 * @deprecated Flux legacy de FCT Dual.
 * Mantingut temporalment mentre es consolida FCTCAP/FCT en Sprint 3.
 */
(function () {
    var profesor = '';

    function getHelpers() {
        return window.intranetUiHelpers || {};
    }

    function getLegacyToken() {
        var tokenElement = document.getElementById('_token');
        return tokenElement ? (tokenElement.textContent || '').trim() : '';
    }

    function getBearerToken() {
        var meta = document.querySelector('meta[name="user-bearer-token"]');
        return meta ? (meta.getAttribute('content') || '').trim() : '';
    }

    function buildApiRequestOptions(method, extraData) {
        var data = extraData || {};
        var headers = {};
        var bearerToken = getBearerToken();
        var legacyToken = getLegacyToken();

        if (bearerToken) {
            headers.Authorization = 'Bearer ' + bearerToken;
        }
        if (legacyToken) {
            data.api_token = legacyToken;
        }

        var searchParams = new URLSearchParams();
        Object.keys(data).forEach(function (key) {
            if (data[key] !== undefined && data[key] !== null) {
                searchParams.append(key, String(data[key]));
            }
        });

        var options = {
            method: method,
            headers: headers
        };

        if (method === 'GET') {
            return { options: options, query: searchParams.toString() };
        }

        headers['Content-Type'] = 'application/x-www-form-urlencoded; charset=UTF-8';
        options.body = searchParams.toString();
        return { options: options, query: '' };
    }

    function requestJson(url, method, data) {
        var request = buildApiRequestOptions(method, data);
        var finalUrl = request.query ? (url + '?' + request.query) : url;

        return fetch(finalUrl, request.options).then(function (response) {
            if (!response.ok) {
                throw new Error('HTTP ' + response.status);
            }
            return response.json();
        });
    }

    function showModal(id) {
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

    function getRowIdFromEventTarget(target) {
        var row = target.closest('tr');
        if (!row || !row.firstElementChild) {
            return '';
        }
        return (row.firstElementChild.textContent || '').trim();
    }

    function updateNextCheckboxState(checkbox, checked) {
        var cell = checkbox.closest('td');
        if (!cell || !cell.nextElementSibling) {
            return;
        }

        var siblingCheckbox = cell.nextElementSibling.querySelector('input[type="checkbox"]');
        if (siblingCheckbox) {
            siblingCheckbox.disabled = !checked;
        }
    }

    function onCheckboxChange(event) {
        var target = event.target;
        if (!target.classList.contains('editor-active')) {
            return;
        }

        var idFct = getRowIdFromEventTarget(target);
        if (!idFct) {
            return;
        }

        var previousValue = !target.checked;
        var checkedValue = target.checked;
        var isA56 = target.classList.contains('a56');
        var payload = { id: idFct };
        payload[isA56 ? 'a56' : 'pg0301'] = checkedValue;

        requestJson('/api/alumnofct/' + idFct, 'PUT', payload).then(function () {
            if (!isA56) {
                updateNextCheckboxState(target, checkedValue);
            }
        }).catch(function (error) {
            target.checked = previousValue;
            window.console.log(error);
        });
    }

    function onMensajeClick(event) {
        var button = event.target.closest('.mensaje');
        if (!button) {
            return;
        }

        event.preventDefault();
        showModal('aviso');

        var id = getRowIdFromEventTarget(button);
        if (!id) {
            return;
        }

        requestJson('/api/alumnofct/' + id, 'GET').then(function (res) {
            var explicacion = document.getElementById('explicacion');
            if (explicacion && res && res.data) {
                explicacion.textContent = 'Revisió Documentació Fct Alumne ' + res.data.alumno + ' :';
            }
            profesor = (res && res.data && res.data.profesor) ? res.data.profesor : '';
        }).catch(function (error) {
            window.console.log(error);
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        var dataFct = document.getElementById('dataFct');
        var formAviso = document.getElementById('formAviso');

        if (dataFct) {
            dataFct.addEventListener('change', onCheckboxChange);
            dataFct.addEventListener('click', onMensajeClick);
        }

        if (formAviso) {
            formAviso.addEventListener('submit', function () {
                formAviso.action = '/profesor/' + profesor + '/mensaje';
            });
        }
    });
})();
