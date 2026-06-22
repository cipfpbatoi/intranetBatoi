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
        if (!bearerToken && legacyToken) {
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

    function setGrupoDocenteEnabled(enabled) {
        var idGrupo = byId('idGrupo_id');
        if (!idGrupo) {
            return;
        }

        idGrupo.disabled = !enabled;
        if (!enabled) {
            idGrupo.value = '';
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

    function applyTipoReunionData(data) {
        setDisabled('grupo_id', Number(data.select) === 0);
        setGrupoDocenteEnabled(data.colectivo === 'Grupo');

        if (data.numeracion) {
            fillNumeroOptions(data.numeracion);
        }
    }

    function loadTipoReunion(tipoValue) {
        if (!tipoValue && tipoValue !== 0) {
            setGrupoDocenteEnabled(false);
            return;
        }

        fetchTipoReunion(tipoValue)
            .then(function (result) {
                applyTipoReunionData(result && result.data ? result.data : {});
            })
            .catch(function (error) {
                window.console.log(error);
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
                setGrupoDocenteEnabled(true);
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

            loadTipoReunion(tipoValue);
        });

        loadTipoReunion(tipo.value);
    });
})();
