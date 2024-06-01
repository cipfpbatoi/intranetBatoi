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
        $("#A5").attr("checked", false).prop('disabled', true);
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
    $(".a1").on("click", function (event) {
        event.preventDefault();
        $(this).attr("data-toggle", "modal").attr("data-target", "#signatura").attr("href", "");
        $("#A2").attr("checked", false).prop('disabled', true);
        $("#A3").attr("checked", false).prop('disabled', true);
        $("#AA3").attr("checked", false).prop('disabled', true);
        var token = $("#_token").text();
        var url = "/api/signatura/a1";
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
    $("#signaturaA1 .submit").click(function() {
        $('#signatura').modal('hide');
        $(this).attr("data-toggle", "modal").attr("data-target", "#loading").attr("href", "");
    });
    $('#file').change(function() {
        // Comprova si s'ha seleccionat algun fitxer
        if ($(this).val()) {
            // Habilita el checkbox
            $('#AA3').prop('disabled', false);
        } else {
            // Deshabilita el checkbox si no hi ha cap fitxer seleccionat (opcional)
            $('#AA3').prop('disabled', true);
        }
    });
    $('#A1').change(function() {
        if (this.checked) {
            $('#A5').prop('checked', false);
        }
    });

    $('#A5').change(function() {
        if (this.checked) {
            $('#A1').prop('checked', false);
        }
    });

});


