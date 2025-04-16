'use strict';

const MODEL = "solicitud";
var id;

$(function () {
    $("#datatable").on("click",'.resolve' ,function (event) {
        event.preventDefault();
        $(this).attr("data-toggle", "modal").attr("data-target", "#resolve").attr("href", "");
        id = $(this).parents(".lineaGrupo").attr("id");
    });
    $("#formResolve").on("submit", function () {
        $(this).attr("action","/solicitud/"+ id + "/resolve");
    });
    $("#explicacion").focus();    
});