'use strict';

const MODEL="colaboracion";
var id;
var list;
var texto;
var day;
var month;

$(function() {
    var token = $("#_token").text();
    $("#tab_descartada").find(".refuse").hide();
    $("#tab_descartada").find(".informe").hide();
    $("#tab_descartada").find(".contacto").hide();
    $("#tab_descartada").find(".switch").siblings().hide();
    $('#tab_pendiente').find(".unauthorize").hide();
    $('#tab_pendiente').find(".switch").siblings().hide();
    $("#tab_pendiente").find(".informe").hide();
    $("#tab_colabora").find(".resolve").hide();
    $("#tab_colabora").find(".switch").siblings().hide();
    $("#tab_colabora").find(".contacto").hide();
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
            boton.siblings(".unauthorize").show();
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
            if (boton.siblings(".switch").length == 0){
                boton.siblings(".contacto").show();
                boton.siblings(".resolve").show();
                boton.siblings(".refuse").show();
            }
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
            boton.siblings(".estado").show();
            if (boton.parents(".profile_details").parent().attr("id") == 'tab_pendiente' ){
                boton.siblings(".contacto").show();
                boton.siblings(".unauthorize").hide();
            }
            if (boton.parents(".profile_details").parent().attr("id") == 'tab_colabora' ){
                boton.siblings(".informe").show();
                boton.siblings(".resolve").hide();
            }
            if (boton.parents(".profile_details").parent().attr("id") == 'tab_descartada' ){
                boton.siblings(".refuse").hide();
                boton.siblings(".unauthorize").hide();
            }

            colaboracion.find(".nombre").text(result.data.nombre+' '+result.data.apellido1+' '+result.data.apellido2);
        });
    });
    $(".btn-info").on("click",function(event){
            if (!confirm('Vas a enviar els correus de manera automàtica:')) {
                event.preventDefault();
            }
    });
    $(".telefonico").on("click",function(event){
        event.preventDefault();
        $(this).attr("data-toggle","modal").attr("data-target", "#dialogo").attr("href","");
        id=$(this).parents(".profile_view").attr("id");
        list = $(this).parents(".profile_view").find(".listActivity");
    });
    $("#formExplicacion").on("submit", function(){
        event.preventDefault();
        $.ajax({
            method: "POST",
            url: "/api/colaboracion/" + id + "/telefonico",
            data: {
                api_token : token,
                explicacion: this.explicacion.value}
        }).then(function (result) {
            texto = list.html();
            day = new Date;
            month = day.getMonth()+1;
            texto = list.html()+"<small>Telèfon- "+day.getDate()+"/"+month+"</small><br/>";
            list.html(texto);
            $("#dialogo").modal('hide');
        }, function (result) {
            console.log("Només es pot un per dia");
            $("#dialogo").modal('hide');
        });
    });
    $('.fa-plus').on("click", function(){
        var id=$(this).parents(".profile_view").attr("id");
        var instructor = $("#idInstructor");
        $('#fctalumnoCreate').attr('action', '/fct/fctalumnoCreate');
        $('#idColaboracion').attr('value',id);
        $.ajax({
            method: "GET",
            url: "/api/colaboracion/instructores/" + id ,
            dataType: 'json',
            data: {api_token: token}
        }).then(function (result) {
                      instructor.empty(); // remove old options
                        $.each(result.data, function (key, value) {
                            instructor.append($("<option></option>")
                                .attr("value", value.dni).text(value.name+' '+value.surnames));
                        });

            }, function (result) {
                console.log("La solicitud no se ha podido completar.");
            });

    });
    $('input[type=text].datetime').datetimepicker({
        sideBySide: true,
        locale: 'es',
        format: 'DD-MM-YYYY LT',
        stepping: 15,
    });
    $('input[type=text].time').datetimepicker({
        sideBySide: true,
        locale: 'es',
        format: 'HH:mm',
        stepping: 15,
    });
    $('input[type=text].date').datetimepicker({
        sideBySide: true,
        locale: 'es',
        format: 'DD-MM-YYYY',
    });

})
