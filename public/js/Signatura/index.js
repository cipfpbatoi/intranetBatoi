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
                pintaTablaSignatura(result.data);
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
                pintaTablaSignatura(result.data);
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
});

