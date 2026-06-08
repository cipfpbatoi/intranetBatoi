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

    function getAuth(extraData) {
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
        var auth = getAuth();
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

    function updateMaterialOptions(result) {
        var material = byId('material_id');
        if (!material) {
            return;
        }

        material.innerHTML = '';

        var defaultOption = document.createElement('option');
        defaultOption.value = '0';
        defaultOption.textContent = 'Escoge un material';
        material.appendChild(defaultOption);

        (result || []).forEach(function (item) {
            var option = document.createElement('option');
            option.value = item.id;
            option.textContent = item.descripcion + '(' + item.id + ')';
            material.appendChild(option);
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        var espacio = byId('espacio_id');
        var material = byId('material_id');
        var descripcion = byId('descripcion_id');
        var tipo = byId('tipo_id');

        if (espacio) {
            espacio.addEventListener('change', function () {
                var idEspacio = espacio.value;
                fetchJson('/api/material/espacio/' + encodeURIComponent(idEspacio))
                    .then(updateMaterialOptions)
                    .catch(function () {
                        window.console.log('La solicitud no se ha podido completar.');
                    });
            });
        }

        if (material && descripcion) {
            material.addEventListener('change', function () {
                var selected = material.options[material.selectedIndex];
                descripcion.value = selected ? selected.text : '';
            });
        }

        if (tipo) {
            tipo.addEventListener('change', function () {
                var tipoId = tipo.value;
                fetchJson('/api/tipoincidencia/' + encodeURIComponent(tipoId))
                    .then(function (result) {
                        var disabled = !!(result && result.data && Number(result.data.tipus) === 2);

                        if (espacio) {
                            espacio.disabled = disabled;
                        }
                        if (material) {
                            material.disabled = disabled;
                        }
                    })
                    .catch(function () {
                        window.console.log('La solicitud no se ha podido completar.');
                    });
            });
        }
    });
})();
