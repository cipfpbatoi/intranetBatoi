'use strict';

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

    var profesor = $('#idProfesor_id').val();


    $("#tipo_id").change(function () {
        var tipo = $('#tipo_id').val();
        var alumnos = $("#idAlumno_id");
        $.ajax({
            method: "GET",
            url: "/api/tipoExpediente/" + tipo,
            dataType: 'json',
            headers: apiAuthOptions().headers,
            data: apiAuthOptions().data
        })
            .then(function (result) {
                var rol = result.data.rol;
                if (rol !== 3) {
                    $('#idModulo_id').prop('disabled', true);
                    $.ajax({
                        method: "GET",
                        url: "/api/alumnoGrupo/" + profesor,
                        dataType: 'json',
                        headers: apiAuthOptions().headers,
                        data: apiAuthOptions().data
                    })
                        .then(function (result) {
                            alumnos.empty(); // remove old options
                            $.each(result, function (key, value) {
                                alumnos.append($("<option></option>")
                                    .attr("value", key).text(value));
                            });
                        });
                } else {
                    $('#idModulo_id').prop('disabled', false);
                    var $el = $("#idAlumno_id");
                    alumnos.empty(); // remove old options
                }

            }, function (result) {
                console.log("La solicitud no se ha podido completar.");
            });
    });
    $("#idModulo_id").change(function () {
        var modulo = $('#idModulo_id').val();
        var alumnos = $("#idAlumno_id");
        $.ajax({
            method: "GET",
            url: "/api/alumnoGrupoModulo/" + profesor + "/" + modulo,
            dataType: 'json',
            headers: apiAuthOptions().headers,
            data: apiAuthOptions().data
        }).then(function (result) {
            alumnos.empty(); // remove old options
            $.each(result, function (key, value) {
                alumnos.append($("<option></option>")
                    .attr("value", value.id).text(value.name));
            });
        }, function (result) {
            console.log("La solicitud no se ha podido completar.");
        });
    });
});
