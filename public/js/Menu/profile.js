$(function () {
    var pageLocale = (($('meta[name="app-locale"]').attr('content') || $('html').attr('lang') || 'es').toLowerCase()).split('-')[0];
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
        },
    }[pickerLocale] || {
        today: 'Today',
        last7: 'Last 7 Days',
        last14: 'Last 14 Days',
        last28: 'Last 28 Days',
        thisMonth: 'This Month',
        lastMonth: 'Last Month'
    };

    function apiAuthOptions(extraData) {
        var legacyToken = $.trim($("#_token").text());
        var bearerToken = $.trim($('meta[name="user-bearer-token"]').attr('content') || "");
        var data = extraData || {};
        var headers = {};

        if (bearerToken) {
            headers.Authorization = "Bearer " + bearerToken;
        }
    if (legacyToken) {
            data.api_token = legacyToken;
        }

        return { headers: headers, data: data };
    }

    function pedir_datos(desde, hasta, profesor) {
        var auth = apiAuthOptions({desde: desde, hasta: hasta, profesor: profesor});
        $.ajax({
            method: "GET",
            url: "api/verficha",
            dataType: "json",
            headers: auth.headers,
            data: auth.data
        }).then(function (result) {
            chart.setData(result.message);
        })
    }
    var chart = new Morris.Bar({

        // ID of the element in which to draw the chart.
        element: 'fichar_bar',
        // Chart data records -- each entry in this array corresponds to a point on
        // the chart.
        // The name of the data record attribute that contains x-values.
        xkey: 'fecha',
        // A list of names of data record attributes that contain y-values.
        ykeys: ['horas'],
        // Labels for the ykeys -- will be displayed when you hover over the
        // chart.
        labels: ['horas'],
    });
    var ahora = new Date();
    var antes = new Date(ahora - (24 * 60 * 60 * 1000) * 14);
    var idProfesor = $('#dniP').data('dni');
    pedir_datos(antes.toJSON().slice(0, 10), ahora.toJSON().slice(0, 10), idProfesor);
    $('input[name="datefilter"]').daterangepicker(
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
                    [labelSet.lastMonth]: [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                }
            },
            function (start, end, label) {
                pedir_datos(start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD'), idProfesor);
            });

});
