'use strict';
$(function() {
    var token = $("#_token").text();
    $("#tab_colabora").find(".resolve").hide();
    $("#tab_descartada").find(".unauthorize").hide();
    $('#tab_pendiente').find(".unauthorize").hide();
    $(".resolve").on("click", function(event){
        event.preventDefault();
        var colaboracion = $(this).parents(".well");
        var boton = $(this);
        $.ajax({
            method: "GET",
            url: "/api/colaboracion/" + colaboracion.attr('id') + "/resolve",
            data: { api_token: token}
        }).then(function (result) {
            $("#tab_colabora").append(colaboracion.parent());
            boton.hide();
            boton.siblings().show();
        });
    });
    $(".refuse").on("click", function(){
        event.preventDefault();
        var colaboracion = $(this).parents(".well");
        var boton = $(this);
        $.ajax({
            method: "GET",
            url: "/api/colaboracion/" + colaboracion.attr('id') + "/refuse",
            data: { api_token: token}
        }).then(function (result) {
            $("#tab_descartada").append(colaboracion.parent());
            boton.hide();
            boton.siblings(".resolve").show();
            boton.siblings(".unauthorize").hide();
        });
    });
    $(".unauthorize").on("click", function(){
        event.preventDefault();
        var colaboracion = $(this).parents(".well");
        var boton = $(this);
        $.ajax({
            method: "GET",
            url: "/api/colaboracion/" + colaboracion.attr('id') + "/unauthorize",
            data: { api_token: token}
        }).then(function (result) {
            $("#tab_pendiente").append(colaboracion.parent());
            boton.hide();
            boton.siblings().show();
        });
    });
})