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

    function clearSelectOptions(select) {
        if (select) {
            select.innerHTML = '';
        }
    }

    function appendOptionsFromObject(select, data) {
        if (!select) {
            return;
        }
        Object.keys(data || {}).forEach(function (key) {
            var option = document.createElement('option');
            option.value = key;
            option.textContent = data[key];
            select.appendChild(option);
        });
    }

    function appendOptionsFromArray(select, data) {
        if (!select) {
            return;
        }
        (data || []).forEach(function (value) {
            var option = document.createElement('option');
            option.value = value.id;
            option.textContent = value.name;
            select.appendChild(option);
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        var profesorInput = byId('idProfesor_id');
        var tipoSelect = byId('tipo_id');
        var moduloSelect = byId('idModulo_id');
        var alumnoSelect = byId('idAlumno_id');
        var profesor = profesorInput ? profesorInput.value : '';

        if (tipoSelect) {
            tipoSelect.addEventListener('change', function () {
                var tipo = tipoSelect.value;

                fetchJson('/api/tipoExpediente/' + encodeURIComponent(tipo))
                    .then(function (result) {
                        var rol = result && result.data ? result.data.rol : null;
                        if (rol !== 3) {
                            if (moduloSelect) {
                                moduloSelect.disabled = true;
                            }
                            return fetchJson('/api/alumnoGrupo/' + encodeURIComponent(profesor))
                                .then(function (alumnos) {
                                    clearSelectOptions(alumnoSelect);
                                    appendOptionsFromObject(alumnoSelect, alumnos);
                                });
                        }

                        if (moduloSelect) {
                            moduloSelect.disabled = false;
                        }
                        clearSelectOptions(alumnoSelect);
                        return null;
                    })
                    .catch(function () {
                        window.console.log('La solicitud no se ha podido completar.');
                    });
            });
        }

        if (moduloSelect) {
            moduloSelect.addEventListener('change', function () {
                var modulo = moduloSelect.value;

                fetchJson('/api/alumnoGrupoModulo/' + encodeURIComponent(profesor) + '/' + encodeURIComponent(modulo))
                    .then(function (result) {
                        clearSelectOptions(alumnoSelect);
                        appendOptionsFromArray(alumnoSelect, result);
                    })
                    .catch(function () {
                        window.console.log('La solicitud no se ha podido completar.');
                    });
            });
        }
    });
})();
