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
        var sao = byId('sao_id');
        var concierto = byId('concierto_id');
        var cif = byId('cif_id');

        if (sao && concierto) {
            sao.addEventListener('change', function () {
                concierto.disabled = !sao.checked;
            });
        }

        if (cif) {
            cif.addEventListener('change', function () {
                var value = (cif.value || '').trim();
                if (!value) {
                    return;
                }

                var auth = buildGetOptions();
                var url = '/api/Empresa/cif=' + encodeURIComponent(value);
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
                        if (result && result.data && result.data[0]) {
                            alert(
                                "Error: CIF duplicat amb l'empresa " +
                                    result.data[0].nombre +
                                    ' de concert ' +
                                    result.data[0].concierto
                            );
                        }
                    })
                    .catch(function () {});
            });
        }
    });
})();
