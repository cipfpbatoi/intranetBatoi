'use strict';

(function () {
    function toIsoDate(date) {
        var year = date.getFullYear();
        var month = String(date.getMonth() + 1).padStart(2, '0');
        var day = String(date.getDate()).padStart(2, '0');
        return year + '-' + month + '-' + day;
    }

    function trim(value) {
        return (value || '').toString().trim();
    }

    function getLegacyToken() {
        var tokenElement = document.getElementById('_token');
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

    function fetchJson(url, auth) {
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
        var localeMeta = document.querySelector('meta[name="app-locale"]');
        var htmlLang = document.documentElement ? document.documentElement.lang : '';
        var pageLocale = ((localeMeta ? localeMeta.getAttribute('content') : '') || htmlLang || 'es').toLowerCase().split('-')[0];
        var pickerLocale = pageLocale === 'en' ? 'en' : (pageLocale === 'ca' ? 'ca' : 'es');

        var chart = new Morris.Bar({
            element: 'fichar_bar',
            xkey: 'fecha',
            ykeys: ['horas'],
            labels: ['horas']
        });

        function pedirDatos(desde, hasta, profesor) {
            var auth = apiAuthOptions({ desde: desde, hasta: hasta, profesor: profesor });
            fetchJson('api/verficha', auth)
                .then(function (result) {
                    chart.setData(result.message || []);
                })
                .catch(function (error) {
                    window.console.log(error);
                });
        }

        var ahora = new Date();
        var antes = new Date(ahora - (24 * 60 * 60 * 1000) * 14);
        var dniNode = document.getElementById('dniP');
        var idProfesor = dniNode ? dniNode.getAttribute('data-dni') : '';

        pedirDatos(antes.toJSON().slice(0, 10), ahora.toJSON().slice(0, 10), idProfesor);

        var datefilterInput = document.querySelector('input[name="datefilter"]');
        if (!datefilterInput) {
            return;
        }

        datefilterInput.style.display = 'none';

        var container = document.createElement('div');
        container.className = 'profile-datefilter-native';
        container.style.display = 'flex';
        container.style.gap = '8px';
        container.style.alignItems = 'center';
        container.style.flexWrap = 'wrap';

        var desdeInput = document.createElement('input');
        desdeInput.type = 'date';
        desdeInput.className = 'form-control';
        desdeInput.value = toIsoDate(antes);

        var hastaInput = document.createElement('input');
        hastaInput.type = 'date';
        hastaInput.className = 'form-control';
        hastaInput.value = toIsoDate(ahora);

        var applyButton = document.createElement('button');
        applyButton.type = 'button';
        applyButton.className = 'btn btn-primary btn-sm';
        applyButton.textContent = pickerLocale === 'en' ? 'Apply' : 'Aplicar';

        function applyRange() {
            var desde = desdeInput.value;
            var hasta = hastaInput.value;
            if (!desde || !hasta) {
                return;
            }
            pedirDatos(desde, hasta, idProfesor);
        }

        applyButton.addEventListener('click', applyRange);
        desdeInput.addEventListener('change', applyRange);
        hastaInput.addEventListener('change', applyRange);

        container.appendChild(desdeInput);
        container.appendChild(hastaInput);
        container.appendChild(applyButton);
        datefilterInput.insertAdjacentElement('afterend', container);
    });
})();
