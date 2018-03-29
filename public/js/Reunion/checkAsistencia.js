'use strict';

$(function () {
    $('.checkbox').on('change', function (event) {
        var idProfesor = $(this).prop('name');
        var token = $("#_token").text();
        var idReunion = $(this).parent(1).parent(1).parent(1).parent(1).parent(1).prop('id');
        var asiste = $(this).prop("checked")?1:0;
        $.ajax({
            method: "PUT",
            url: "/api/asistencia/cambiar",
            data: {
                idProfesor: idProfesor,
                idReunion: idReunion,
                asiste: asiste,
                api_token: token,
            },
        }).then(function (res) {
            console.log(res)
        }, function (res) {
            if (asiste) {
                $(this).removeProp("checked");
            } else {
                $(this).prop("checked");
            }
            console.log(res)
        });
    })
});
