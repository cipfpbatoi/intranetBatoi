'use strict';
var href;
$(function () {

    $(".signatura").on("click", function (event) {
        event.preventDefault();
        $(this).attr("data-toggle", "modal").attr("data-target", "#signatura").attr("href", "");
        var token = $("#_token").text();
        var url = "/api/signatura/director";
        $.ajax({
            method: "GET",
            url: url,
            dataType: 'json',
            data: {api_token: token}
        })
            .then(function (result) {
                pintaTablaSeleccion(result.data,"#tableSignatura");
            }, function (result) {
                console.log("La solicitud no se ha podido completar.");
            });
    });
    $(".sign").on("click", function (event) {
        event.preventDefault();
        $(this).attr("data-toggle", "modal").attr("data-target", "#signatura").attr("href", "");
        var token = $("#_token").text();
        var url = "/api/signatura";
        $.ajax({
            method: "GET",
            url: url,
            dataType: 'json',
            data: {api_token: token}
        })
            .then(function (result) {
                pintaTablaSeleccion(result.data,"#tableSignatura");
            }, function (result) {
                console.log("La solicitud no se ha podido completar.");
            });
    });
    $(".up").on("click", function (event) {
        event.preventDefault();
        href = $(this).parents("a").attr("href");
        $(this).attr("data-toggle", "modal").attr("data-target", "#upload").attr("href", "");
    });
    $("#formUpload").on("submit", function(){
        $(this).attr("action",href);
    });
    $("#signatura .submit").click(function() {
        $('#signatura').modal('hide');
        $(this).attr("data-toggle", "modal").attr("data-target", "#loading").attr("href", "");
    });
    $('#file').change(function() {
        // Comprova si s'ha seleccionat algun fitxer
        if ($(this).val()) {
            // Habilita el checkbox
            $('#A3').prop('disabled', false);
        } else {
            // Deshabilita el checkbox si no hi ha cap fitxer seleccionat (opcional)
            $('#A3').prop('disabled', true).prop('checked', false);
        }
    });
});


