'use strict';

(function () {
    function byId(id) {
        return document.getElementById(id);
    }

    function trim(value) {
        return (value || '').toString().trim();
    }

    function getLegacyToken() {
        var tokenElement = byId('_token');
        return tokenElement ? trim(tokenElement.textContent) : '';
    }

    function getBearerToken() {
        var meta = document.querySelector('meta[name="user-bearer-token"]');
        return meta ? trim(meta.getAttribute('content')) : '';
    }

    function apiAuthOptions() {
        var options = {
            headers: {},
            data: {}
        };

        var bearerToken = getBearerToken();
        var legacyToken = getLegacyToken();

        if (bearerToken) {
            options.headers.Authorization = 'Bearer ' + bearerToken;
        }
        if (legacyToken) {
            options.data.api_token = legacyToken;
        }

        return options;
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

    document.addEventListener('DOMContentLoaded', function () {
        var modulo = byId('idModulo_id');
        var ciclo = byId('ciclo_id');
        if (!modulo || !ciclo) {
            return;
        }

        modulo.addEventListener('change', function () {
            var moduloId = modulo.value;
            var auth = apiAuthOptions();

            fetchJson('/api/modulo/' + encodeURIComponent(moduloId), auth)
                .then(function (result) {
                    return fetchJson('/api/ciclo/' + encodeURIComponent(result.data.idCiclo), auth);
                })
                .then(function (result) {
                    ciclo.value = result.data.ciclo || '';
                })
                .catch(function () {
                    window.console.log('La solicitud no se ha podido completar.');
                });
        });
    });
})();
