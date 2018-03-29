'use strict';
$(function () {
    $("#idModulo_id").change(function () {
        var modulo = $('#idModulo_id').val();
        var token = $("#_token").text();
        $.ajax({
            method: "GET",
            url: "/api/modulo/" + modulo,
            dataType: 'json',
            data: {api_token: token},
        })
            .then(function (result) {
                var ciclo = result.data.idCiclo;
                $.ajax({
                    method: "GET",
                    url: "/api/ciclo/" + ciclo,
                    dataType: 'json',
                    data: {api_token: token},
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


