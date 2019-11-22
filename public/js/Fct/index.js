'use strict';

const MODEL = "fct";
var id;

$(function () {
    $(".pdf").on("click", function (event) {
        event.preventDefault();
        $(this).attr("data-toggle", "modal").attr("data-target", "#fechas").attr("href", "");
        id = $(this).parents(".lineaGrupo").attr("id");
    });
    $("#formFechas").on("submit", function () {
        $(this).attr("action", "/" + MODEL + "/" + id + "/pdf");
    });
});