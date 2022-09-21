'use strict';

const MODEL = "solicitud";
var id;

$(function () {
    $(".resolve").on("click", function (event) {
        event.preventDefault();
        $(this).attr("data-toggle", "modal").attr("data-target", "#aviso").attr("href", "");
        id = $(this).parents(".lineaGrupo").attr("id");
    });
    $("#formAviso").on("submit", function () {
        $(this).attr("action","/solicitud/"+ id + "/resolve");
    });
    $("#explicacion").focus();    
});