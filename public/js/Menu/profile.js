'use strict';

(function () {
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
        var jq = window.jQuery || window.$;
        if (!jq) {
            return;
        }

        var pageLocale = ((jq('meta[name="app-locale"]').attr('content') || jq('html').attr('lang') || 'es').toLowerCase()).split('-')[0];
        var pickerLocale = pageLocale === 'en' ? 'en' : (pageLocale === 'ca' ? 'ca' : 'es');
        var dateRangeFormat = pageLocale === 'en' ? 'MM/DD/YYYY' : 'DD/MM/YYYY';

        var labelSet = {
            en: {
                today: 'Today',
                last7: 'Last 7 Days',
                last14: 'Last 14 Days',
                last28: 'Last 28 Days',
                thisMonth: 'This Month',
                lastMonth: 'Last Month'
            },
            es: {
                today: 'Hoy',
                last7: 'Últimos 7 días',
                last14: 'Últimos 14 días',
                last28: 'Últimos 28 días',
                thisMonth: 'Este mes',
                lastMonth: 'Mes anterior'
            },
            ca: {
                today: 'Avui',
                last7: 'Últims 7 dies',
                last14: 'Últims 14 dies',
                last28: 'Últims 28 dies',
                thisMonth: 'Aquest mes',
                lastMonth: 'Mes anterior'
            }
        }[pickerLocale] || {
            today: 'Today',
            last7: 'Last 7 Days',
            last14: 'Last 14 Days',
            last28: 'Last 28 Days',
            thisMonth: 'This Month',
            lastMonth: 'Last Month'
        };

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

        jq('input[name="datefilter"]').daterangepicker(
            {
                locale: {
                    format: dateRangeFormat,
                    applyLabel: pickerLocale === 'en' ? 'Apply' : 'Aplicar',
                    cancelLabel: pickerLocale === 'en' ? 'Clear' : 'Netejar',
                    fromLabel: pickerLocale === 'en' ? 'From' : 'Des de',
                    toLabel: pickerLocale === 'en' ? 'To' : 'Fins',
                    customRangeLabel: pickerLocale === 'en' ? 'Custom' : 'Personalitzat'
                },
                startDate: ahora,
                endDate: antes,
                ranges: {
                    [labelSet.today]: [moment().startOf('day'), moment().endOf('day')],
                    [labelSet.last7]: [moment().subtract(6, 'days'), moment()],
                    [labelSet.last14]: [moment().subtract(14, 'days'), moment()],
                    [labelSet.last28]: [moment().subtract(28, 'days'), moment()],
                    [labelSet.thisMonth]: [moment().startOf('month'), moment().endOf('month')],
                    [labelSet.lastMonth]: [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
                }
            },
            function (start, end) {
                pedirDatos(start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD'), idProfesor);
            }
        );
    });
})();
