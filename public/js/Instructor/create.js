'use strict';

$(function () {
    $('#dni_id').change(function () {
        var dni = $("#dni_id").val();
        var token = $("#_token").text();
        $.ajax({
            method: "GET",
            url: "/api/instructor/"+dni,
            data: {api_token: token},
        })
        .then(function (result) {
            $('#nombre_id').val(result.data.nombre);
            $('#email_id').val(result.data.email);
            $('#telefono_id').val(result.data.telefono);
        }, function (result) {
            console.log("La solicitud no se ha podido completar.");
        });
    });
});