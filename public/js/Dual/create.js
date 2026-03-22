/**
 * @deprecated Flux legacy de DUAL/FCTDUAL.
 * Mantingut temporalment per compatibilitat.
 */
'use strict';

(function () {
    function byId(id) {
        return document.getElementById(id);
    }

    function getLegacyToken() {
        var tokenElement = byId('_token');
        return tokenElement ? (tokenElement.textContent || '').trim() : '';
    }

    function getBearerToken() {
        var meta = document.querySelector('meta[name="user-bearer-token"]');
        return meta ? (meta.getAttribute('content') || '').trim() : '';
    }

    function apiAuthOptions(extraData) {
        var data = extraData ? Object.assign({}, extraData) : {};
        var headers = {};
        var bearerToken = getBearerToken();
        var legacyToken = getLegacyToken();

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

    function fetchJson(url) {
        var auth = apiAuthOptions();
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

    function fillInstructorOptions(colaboracionId, selectedInstructor) {
        var select = byId('idInstructor_id');
        if (!select || !colaboracionId) {
            return;
        }

        fetchJson('/api/colaboracion/instructores/' + encodeURIComponent(colaboracionId))
            .then(function (result) {
                var options = (result && result.data) ? result.data : [];
                select.innerHTML = '';

                options.forEach(function (value) {
                    var option = document.createElement('option');
                    option.value = value.dni;
                    option.textContent = value.name + ' ' + value.surnames;
                    if (selectedInstructor && String(selectedInstructor) === String(value.dni)) {
                        option.selected = true;
                    }
                    select.appendChild(option);
                });
            })
            .catch(function () {
                window.console.log('La solicitud no se ha podido completar.');
            });
    }

    document.addEventListener('DOMContentLoaded', function () {
        var colaboracion = byId('idColaboracion_id');
        if (colaboracion) {
            colaboracion.addEventListener('change', function () {
                fillInstructorOptions(colaboracion.value, null);
            });
        }
    });

    window.postModal = function () {
        var colaboracion = byId('idColaboracion_id');
        var instructor = byId('idInstructor_id');
        if (!colaboracion || !instructor || !colaboracion.value) {
            return;
        }

        fillInstructorOptions(colaboracion.value, instructor.value);
    };
})();
