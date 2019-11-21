'use strict';

$(function () {

    var token = $("#_token").text();
    var profesor = $('#idProfesor_id').val();


    $("#tipo_id").change(function () {
        var tipo = $('#tipo_id').val();
        var alumnos = $("#idAlumno_id");
        $.ajax({
            method: "GET",
            url: "/api/tipoExpediente/" + tipo,
            dataType: 'json',
            data: {api_token: token}
        })
            .then(function (result) {
                var rol = result.data.rol;
                if (rol !== 3) {
                    $('#idModulo_id').prop('disabled', true);
                    $.ajax({
                        method: "GET",
                        url: "/api/alumnoGrupo/" + profesor,
                        dataType: 'json',
                        data: {api_token: token}
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
            data: {api_token: token}
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