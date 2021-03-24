'use strict';

const MODEL = "alumnofct";
var id;

$(function() {
    $("#401").on("click", function(event){
        event.preventDefault();
        $(this).attr("data-toggle","modal").attr("data-target", "#entreFechas").attr("href","");
        id='pr0401';
    });
    $("#402").on("click", function(event){
        event.preventDefault();
        $(this).attr("data-toggle","modal").attr("data-target", "#entreFechas").attr("href","");
        id= 'pr0402';
    });
    $("#formEntreFechas").on("submit", function(){
        $(this).attr("action","fct/"+id+"/print");
    });

    /*
    $(".pdf").on("click", function (event) {
        event.preventDefault();
        $(this).attr("data-toggle", "modal").attr("data-target", "#fechas").attr("href", "");
        id = $(this).parents(".lineaGrupo").attr("id");
    });
    $("#formFecha").on("submit", function () {
        $(this).attr("action", MODEL + "/" + id + "/pdf");
    });*/
})
