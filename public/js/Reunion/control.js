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

    function fetchTipoReunion(tipo) {
        var auth = apiAuthOptions();
        return fetch(withQueryParams('/api/tiporeunion/' + encodeURIComponent(tipo), auth.data), {
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

    function updateNumeroOptions(numeracion) {
        var numero = byId('numero_id');
        if (!numero) {
            return;
        }

        numero.innerHTML = '';
        Object.keys(numeracion || {}).forEach(function (key) {
            var option = document.createElement('option');
            option.value = key;
            option.textContent = numeracion[key];
            numero.appendChild(option);
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        var tipo = byId('tipo_id');
        if (!tipo) {
            return;
        }

        tipo.addEventListener('change', function () {
            fetchTipoReunion(tipo.value)
                .then(function (result) {
                    if (result && result.data && result.data.numeracion) {
                        updateNumeroOptions(result.data.numeracion);
                    }
                })
                .catch(function (error) {
                    window.console.log(error);
                });
        });
    });
})();
