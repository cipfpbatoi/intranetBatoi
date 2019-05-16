'use strict';
$(function() {
    var token = $("#_token").text();
    $("#tab_colabora").find(".resolve").hide();
    $("#tab_descartada").find(".unauthorize").hide();
    $("#tab_descartada").find(".refuse").hide();
    $('#tab_pendiente').find(".unauthorize").hide();
    $("#tab_colabora").find(".switch").siblings(".informe").hide();
    $("#tab_colabora").find(".contacto").hide();
    $("#tab_descartada").find(".informe").hide();
    $("#tab_descartada").find(".contacto").hide();
    $('#tab_pendiente').find(".switch").siblings(".contacto").hide();
    $("#tab_pendiente").find(".informe").hide();
    $(".resolve").on("click", function(event){
        event.preventDefault();
        var colaboracion = $(this).parents(".well");
        var boton = $(this);
        $.ajax({
            method: "GET",
            url: "/api/colaboracion/" + colaboracion.attr('id') + "/resolve",
            data: { api_token: token}
        }).then(function (result) {
            boton.hide();
            boton.siblings(".unauthorize").show();
            boton.siblings(".refuse").show();
            boton.siblings(".contacto").hide();
            if (boton.siblings(".switch").length == 0) boton.siblings(".informe").show();
            $("#tab_colabora").append(colaboracion.parent());
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
            boton.hide();
            boton.siblings(".resolve").show();
            boton.siblings(".unauthorize").hide();
            boton.siblings(".informe").hide();
            boton.siblings(".contacto").hide();
            $("#tab_descartada").append(colaboracion.parent());
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

            boton.hide();
            if (! boton.find(".switch")) boton.siblings(".contacto").show();
            boton.siblings(".informe").hide();
            $("#tab_pendiente").append(colaboracion.parent());
        });
    });
    $(".switch").on("click", function(){
        event.preventDefault();
        var colaboracion = $(this).parents(".well");
        var boton = $(this);
        $.ajax({
            method: "GET",
            url: "/api/colaboracion/" + colaboracion.attr('id') + "/switch",
            data: { api_token: token}
        }).then(function (result) {
            boton.hide();
            if (boton.parents(".profile_details").parent().attr("id") == 'tab_pendiente' )
                boton.siblings(".contacto").show();
            if (boton.parents(".profile_details").parent().attr("id") == 'tab_colabora' )
                boton.siblings(".informe").show();
            colaboracion.find(".nombre").text(result.data.nombre+' '+result.data.apellido1+' '+result.data.apellido2);
        });
    });
    $(".btn-info").on("click",function(event){
            if (!confirm('Vas a enviar els correus de manera automàtica:')) {
                event.preventDefault();
            }
    });
})