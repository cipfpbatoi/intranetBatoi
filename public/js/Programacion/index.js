'use strict';
$(function () {
    function apiAuthOptions() {
        var legacyToken = $.trim($("#_token").text());
        var bearerToken = $.trim($('meta[name="user-bearer-token"]').attr('content') || "");
        var options = {
            headers: {},
            data: {}
        };

        if (bearerToken) {
            options.headers.Authorization = "Bearer " + bearerToken;
        } else if (legacyToken) {
            options.data.api_token = legacyToken;
        }

        return options;
    }

    $("#idModulo_id").change(function () {
        var modulo = $('#idModulo_id').val();
        var auth = apiAuthOptions();
        $.ajax({
            method: "GET",
            url: "/api/modulo/" + modulo,
            dataType: 'json',
            headers: auth.headers,
            data: auth.data,
        })
            .then(function (result) {
                var ciclo = result.data.idCiclo;
                $.ajax({
                    method: "GET",
                    url: "/api/ciclo/" + ciclo,
                    dataType: 'json',
                    headers: auth.headers,
                    data: auth.data,
                })
                    .then(function (result) {
                        $("#ciclo_id").empty().val(result.data.ciclo);
                    }, function (result) {
                        console.log("La solicitud no se ha podido completar.");
                    });
            }, function (result) {
                console.log("La solicitud no se ha podido completar.");
            });
    });
});

