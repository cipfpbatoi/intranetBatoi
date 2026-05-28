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

    function toFormBody(data) {
        var params = new URLSearchParams();
        Object.keys(data || {}).forEach(function (key) {
            if (data[key] !== undefined && data[key] !== null) {
                params.append(key, String(data[key]));
            }
        });
        return params.toString();
    }

    function getAncestorId(element) {
        var current = element;
        while (current && current !== document.body) {
            if (current.id) {
                return current.id;
            }
            current = current.parentElement;
        }
        return '';
    }

    function updateAsistencia(payload) {
        var auth = apiAuthOptions(payload);
        var headers = Object.assign({}, auth.headers, {
            'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
        });

        return fetch('/api/asistencia/cambiar', {
            method: 'PUT',
            headers: headers,
            body: toFormBody(auth.data),
            credentials: 'same-origin'
        }).then(function (response) {
            if (!response.ok) {
                throw new Error('HTTP ' + response.status);
            }
            return response.text();
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.checkbox').forEach(function (checkbox) {
            checkbox.addEventListener('change', function () {
                var asiste = checkbox.checked ? 1 : 0;
                var previous = !checkbox.checked;
                var payload = {
                    idProfesor: checkbox.name,
                    idReunion: getAncestorId(checkbox),
                    asiste: asiste
                };

                updateAsistencia(payload)
                    .then(function (result) {
                        window.console.log(result);
                    })
                    .catch(function (error) {
                        checkbox.checked = previous;
                        window.console.log(error);
                    });
            });
        });
    });
})();
