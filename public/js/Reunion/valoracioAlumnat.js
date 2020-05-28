'use strict';
$(function () {
    $('.valoraciones').on('change', function (event) {
        var idAlumno = $(this).parent(1).siblings().first().text();
        var token = $("#_token").text();
        var idReunion = $(this).parent(1).parent(1).parent(1).parent(1).parent(1).prop('id');
        var capacitats = $(this).val();
        $.ajax({
            method: "PUT",
            url: "/api/reunion/"+idReunion+"/alumno/"+idAlumno,
            data: {
                capacitats: capacitats,
                api_token: token,
            },
        }).then(function (res) {
            console.log(res)
        }, function (res) {
            console.log(res)
        });
    })
});