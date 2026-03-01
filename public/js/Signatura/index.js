'use strict';
var href;
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

    $(".signatura").on("click", function (event) {
        event.preventDefault();
        $(this).attr("data-toggle", "modal").attr("data-target", "#signatura").attr("href", "");
        var url = "/api/signatura/director";
        var auth = apiAuthOptions();
        $.ajax({
            method: "GET",
            url: url,
            dataType: 'json',
            headers: auth.headers,
            data: auth.data
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
        var url = "/api/signatura";
        var auth = apiAuthOptions();
        $.ajax({
            method: "GET",
            url: url,
            dataType: 'json',
            headers: auth.headers,
            data: auth.data
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
        var url = "/api/signatura/a1";
        var auth = apiAuthOptions();
        $.ajax({
            method: "GET",
            url: url,
            dataType: 'json',
            headers: auth.headers,
            data: auth.data
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

