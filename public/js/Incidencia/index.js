'use strict';

const MODEL = "incidencia";
var id;

$(function () {
    $(".refuse").on("click", function (event) {
        event.preventDefault();
        $(this).attr("data-toggle", "modal").attr("data-target", "#dialogo").attr("href", "");
        id = $(this).parents(".profile_view").attr("id");
    });
    $("#formExplicacion").on("submit", function () {
        $(this).attr("action", MODEL + "/" + id + "/refuse");
    });

    $(".resolve").on("click", function (event) {
        event.preventDefault();
        $(this).attr("data-toggle", "modal").attr("data-target", "#aviso").attr("href", "");
        id = $(this).parents(".profile_view").attr("id");
    });
    $("#formAviso").on("submit", function () {
        $(this).attr("action","/mantenimiento/" + MODEL + "/" + id + "/resolve");
    });
    $("#explicacion").focus();    
});

function getToken() {
    var ppio = document.cookie.indexOf("XSRF-TOKEN=");
    if (ppio === -1)
        return "";
    else
        ppio += 11;	// para no coger el nombre de la cookie
    var fin = document.cookie.indexOf(";", ppio);
    if (fin === -1)
        fin = document.cookie.length;
    return document.cookie.substring(ppio, fin);
}

 