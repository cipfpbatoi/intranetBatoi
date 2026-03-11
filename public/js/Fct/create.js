(function () {
    'use strict';

    function trim(value) {
        return (value || '').toString().trim();
    }

    function getApiAuthOptions(extraData) {
        if (typeof window.apiAuthOptions === 'function') {
            return window.apiAuthOptions(extraData);
        }

        var legacyTokenEl = document.querySelector('#_token');
        var legacyToken = trim(legacyTokenEl ? legacyTokenEl.textContent : '');
        var bearerMeta = document.querySelector('meta[name="user-bearer-token"]');
        var bearerToken = trim(bearerMeta ? bearerMeta.getAttribute('content') : '');
        var data = extraData ? Object.assign({}, extraData) : {};
        var headers = {};

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

    function fetchJsonGet(url, auth) {
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
        var colaboracionSelect = document.getElementById('idColaboracion_id');
        var instructorSelect = document.getElementById('idInstructor_id');

        if (!colaboracionSelect || !instructorSelect) {
            return;
        }

        colaboracionSelect.addEventListener('change', function () {
            var idColaboracion = colaboracionSelect.value;
            var auth = getApiAuthOptions();

            fetchJsonGet('/api/colaboracion/instructores/' + idColaboracion, auth)
                .then(function (result) {
                    var newOptions = result.data || [];
                    instructorSelect.innerHTML = '';

                    newOptions.forEach(function (value) {
                        var option = document.createElement('option');
                        option.value = value.dni;
                        option.textContent = value.name + ' ' + value.surnames;
                        instructorSelect.appendChild(option);
                    });
                })
                .catch(function () {
                    console.log('La solicitud no se ha podido completar.');
                });
        });
    });
})();
