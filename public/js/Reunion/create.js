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

    function setDisabled(id, disabled) {
        var element = byId(id);
        if (element) {
            element.disabled = disabled;
        }
    }

    function fillNumeroOptions(numeracion) {
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
        var descripcion = byId('descripcion_id');

        if (!tipo) {
            return;
        }

        tipo.addEventListener('change', function () {
            var tipoValue = tipo.value;

            if (String(tipoValue) === '9') {
                setDisabled('fichero_id', false);
                setDisabled('numero_id', true);
                if (descripcion) {
                    descripcion.value = 'Acta FSE';
                }
                setDisabled('objetivos_id', true);
                setDisabled('grupo_id', true);
                return;
            }

            setDisabled('fichero_id', true);
            setDisabled('numero_id', false);
            setDisabled('objetivos_id', false);

            fetchTipoReunion(tipoValue)
                .then(function (result) {
                    var data = result && result.data ? result.data : {};
                    setDisabled('grupo_id', Number(data.select) === 0);

                    if (data.numeracion) {
                        fillNumeroOptions(data.numeracion);
                    }
                })
                .catch(function (error) {
                    window.console.log(error);
                });
        });
    });
})();
