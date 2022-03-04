'use strict';

const MODEL="colaboracion";
var id;
var list;
var texto;
var day;
var month;
var tipo;

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
    $(".telefonico").on("click",function(event){
        event.preventDefault();
        $(this).attr("data-toggle","modal").attr("data-target", "#dialogo").attr("href","");
        id=$(this).parents(".profile_view").find(".fct").attr("id");
        list = $(this).parents(".profile_view").find(".listActivity");
        tipo = 'telefonico';
    });
    $(".small").on("click",function(event){
        event.preventDefault();
        id=$(this).attr("id");
        $.ajax({
            method: "GET",
            url: "/api/activity/" + id ,
            data: {
                api_token: token,
            }
        }).then(function (result) {
            $("#dialogo").find("#explicacion").val(result.data.comentari);
        }, function (result) {
            console.log("Error al buscarr");
        });
        $(this).attr("data-toggle","modal").attr("data-target", "#dialogo").attr("href","");
        tipo = 'seguimiento';
    });
    $("#formDialogo").on("submit", function(){
        event.preventDefault();
        if (tipo == 'telefonico') {
            $.ajax({
                method: "POST",
                url: "/api/colaboracion/" + id + "/telefonico",
                data: {
                    api_token: token,
                    explicacion: this.explicacion.value
                }
            }).then(function (result) {
                texto = list.html();
                day = new Date;
                month = day.getMonth() + 1;
                texto = list.html() + "<small>Telèfon- " + day.getDate() + "/" + month + "</small><br/>";
                list.html(texto);
                $("#dialogo").modal('hide');
            }, function (result) {
                console.log("Només es pot un per dia");
                $("#dialogo").modal('hide');
            });
        }
        if (tipo == 'seguimiento'){
            $.ajax({
                method: "PUT",
                url: "/api/activity/" + id ,
                data: {
                    api_token: token,
                    comentari: this.explicacion.value
                }
            }).then(function (result) {
                $("#dialogo").modal('hide');
            }, function (result) {
                console.log("Error al modificar");
                $("#dialogo").modal('hide');
            });
        }
    });
    $('.fa-plus').on("click", function(){
        var id=$(this).parents(".profile_view").attr("id");
        var instructor = $("#idInstructor");
        $('#formAddAlumno').attr('action', '/fct/fctalumnoCreate');
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
    Array.from(document.querySelectorAll('.dragable')).forEach((item)=>{
        item.setAttribute('draggable','draggable');
        item.addEventListener('dragstart',(event)=>{
            //event.preventDefault();
            event.dataTransfer.setData('text/plain',event.target.id);
        });

    });
    Array.from(document.querySelectorAll('.fct')).forEach((item)=>{
        item.addEventListener('dragover',(event)=>{
            event.preventDefault();
        });
        item.addEventListener('drop',(event)=>{
            event.preventDefault();
            let id = event.dataTransfer.getData('text/plain');
            let newFct = event.currentTarget;
            var token = $("#_token").text();
            if (confirm('Vas a moure esta evidencia a una altra FCT')){
                $.ajax({
                    method: "GET",
                    url: "/api/activity/"+id+"/move/" + newFct.id ,
                    dataType: 'json',
                    data: {api_token: token}
                }).then(function (result) {
                    newFct.querySelector('.listActivity').appendChild(document.getElementById(id).parentElement);
                }, function (result) {
                    alert("La sol·licitut no s'ha pogut completar: "+result.responseText);
                });
            }
        });
    });
    /**
    $('.dragable').draggable({
        container: 'document',
        cursor: 'move',
        opacity: 0.70,
        zIndex:10000,
        appendTo: ".fct",
        revert: "invalid",
        revertDuration: 200,
        helper: this.id,
    });
    $('.fct').droppable( {
        drop: handleDropEvent
    } );
    */
})

/**
function handleDropEvent( event, ui ) {
    var token = $("#_token").text();
    var newFct = this.id;
    var oldFct = ui.draggable.context.id;
    //var element = ui.draggable.data( 'number' );
    if (confirm('Vas a moure esta evidencia a una altra FCT')){
        $.ajax({
            method: "GET",
            url: "/api/activity/"+oldFct+"/move/" + newFct ,
            dataType: 'json',
            data: {api_token: token}
        }).then(function (result) {
            alert('La sol·licitut ha estat completada');
        }, function (result) {
            alert("La sol·licitut no s'ha pogut completar: "+result.responseText);
            location.reload();
        });
    }
}
*/
