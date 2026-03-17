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

    function getAlumnoId(select) {
        var cell = select.closest('td');
        if (!cell || !cell.parentElement || !cell.parentElement.firstElementChild) {
            return '';
        }
        return trim(cell.parentElement.firstElementChild.textContent);
    }

    function updateValoracion(idReunion, idAlumno, capacitats) {
        var auth = apiAuthOptions({ capacitats: capacitats });
        var headers = Object.assign({}, auth.headers, {
            'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
        });

        return fetch('/api/reunion/' + idReunion + '/alumno/' + idAlumno, {
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
        document.querySelectorAll('.valoraciones').forEach(function (select) {
            select.addEventListener('change', function () {
                var idAlumno = getAlumnoId(select);
                var idReunion = getAncestorId(select);
                var capacitats = select.value;

                if (!idAlumno || !idReunion) {
                    return;
                }

                updateValoracion(idReunion, idAlumno, capacitats)
                    .then(function (result) {
                        window.console.log(result);
                    })
                    .catch(function (error) {
                        window.console.log(error);
                    });
            });
        });
    });
})();
