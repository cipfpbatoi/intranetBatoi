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

    function buildGetOptions(extraData) {
        var data = extraData || {};
        var headers = {};
        var bearerToken = getBearerToken();
        var legacyToken = getLegacyToken();
        var params = new URLSearchParams();

        if (bearerToken) {
            headers.Authorization = 'Bearer ' + bearerToken;
        }
        if (legacyToken) {
            data.api_token = legacyToken;
        }

        Object.keys(data).forEach(function (key) {
            if (data[key] !== undefined && data[key] !== null) {
                params.append(key, String(data[key]));
            }
        });

        return { headers: headers, query: params.toString() };
    }

    document.addEventListener('DOMContentLoaded', function () {
        var dni = byId('dni_id');
        var name = byId('name_id');
        var surnames = byId('surnames_id');
        var email = byId('email_id');
        var telefono = byId('telefono_id');

        if (!dni) {
            return;
        }

        dni.addEventListener('change', function () {
            var value = (dni.value || '').trim();
            if (!value) {
                return;
            }

            var auth = buildGetOptions();
            var url = '/api/instructor/' + encodeURIComponent(value);
            if (auth.query) {
                url += '?' + auth.query;
            }

            fetch(url, { method: 'GET', headers: auth.headers })
                .then(function (response) {
                    if (!response.ok) {
                        throw new Error('HTTP ' + response.status);
                    }
                    return response.json();
                })
                .then(function (result) {
                    if (!result || !result.data) {
                        return;
                    }

                    if (name) {
                        name.value = result.data.name || '';
                    }
                    if (surnames) {
                        surnames.value = result.data.surnames || '';
                    }
                    if (email) {
                        email.value = result.data.email || '';
                    }
                    if (telefono) {
                        telefono.value = result.data.telefono || '';
                    }
                })
                .catch(function () {
                    window.console.log('La solicitud no se ha podido completar.');
                });
        });
    });
})();
