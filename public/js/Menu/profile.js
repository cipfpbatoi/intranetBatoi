$(function () {
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
                    format: 'DD-MM-YYYY'
                },
                startDate: ahora,
                endDate: antes,
                ranges: {
                    'Last 7 Days': [moment().subtract(6, 'days'), moment()],
                    'Last 14 Days': [moment().subtract(14, 'days'), moment()],
                    'Last 28 Days': [moment().subtract(28, 'days'), moment()],
                    'This Month': [moment().startOf('month'), moment().endOf('month')],
                    'Last Month': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                }
            },
            function (start, end, label) {
                pedir_datos(start.format('YYYY-MM-DD'), end.format('YYYY-MM-DD'), idProfesor);
            });

});

