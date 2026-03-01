'use strict';

const MODEL="colaboracion";
var id;
var col;
var list;
var texto;
var day;
var month;
var tipo;

function apiAuthOptions(extraData) {
    var legacyToken = $.trim($("#_token").text());
    var bearerToken = $.trim($('meta[name="user-bearer-token"]').attr('content') || "");
    var data = extraData || {};
    var headers = {};

    if (bearerToken) {
        headers.Authorization = "Bearer " + bearerToken;
    }
    if (legacyToken) {
        data.api_token = legacyToken;
    }

    return { headers: headers, data: data };
}

$(function() {
    $("#tab_colabora").find(".resolve").hide();
    $('#tab_pendiente').find(".unauthorize").hide();
    $('#tab_resta').find(".book").hide();
    $(".resolve").on("click", function(event){
        event.preventDefault();
        var colaboracion = $(this).parents(".well");
        var boton = $(this);
        $.ajax({
            method: "GET",
            url: "/api/colaboracion/" + colaboracion.attr('id') + "/resolve",
            headers: apiAuthOptions().headers,
            data: apiAuthOptions().data
        }).then(function () {
            boton.hide();
            boton.siblings(".unauthorize").show();
            boton.siblings(".refuse").show();
            colaboracion.attr('style','border-color: #1abb9c;border-width: medium');
            if (boton.parents(".profile_details").parent().attr("id") !== 'tab_colabora' ) {
                $("#tab_colabora").append(colaboracion.parent());
            }
        });
    });
    $(".refuse").on("click", function(event){
        event.preventDefault();
        var colaboracion = $(this).parents(".well");
        var boton = $(this);
        $.ajax({
            method: "GET",
            url: "/api/colaboracion/" + colaboracion.attr('id') + "/refuse",
            headers: apiAuthOptions().headers,
            data: apiAuthOptions().data
        }).then(function () {
            boton.hide();
            boton.siblings(".resolve").show();
            boton.siblings(".unauthorize").show();
            colaboracion.attr('style','border-color: #90111a;border-width: medium');
            if (boton.parents(".profile_details").parent().attr("id") !== 'tab_pendiente' ) {
                $("#tab_pendiente").append(colaboracion.parent());
            }
        });
    });
    $(".unauthorize").on("click", function(event){
        event.preventDefault();
        var colaboracion = $(this).parents(".well");
        var boton = $(this);
        $.ajax({
            method: "GET",
            url: "/api/colaboracion/" + colaboracion.attr('id') + "/unauthorize",
            headers: apiAuthOptions().headers,
            data: apiAuthOptions().data
        }).then(function () {
            boton.hide();
            if (boton.siblings(".switch").length === 0){
                boton.siblings(".resolve").show();
                boton.siblings(".refuse").show();
            }
            colaboracion.attr('style','border-color: #00aeef;border-width: medium');
            if (boton.parents(".profile_details").parent().attr("id") !== 'tab_pendiente' ) {
                $("#tab_pendiente").append(colaboracion.parent());
            }
        });
    });
    $(".switch").on("click", function(event){
        event.preventDefault();
        var colaboracion = $(this).parents(".well");
        var boton = $(this);
        $.ajax({
            method: "GET",
            url: "/api/colaboracion/" + colaboracion.attr('id') + "/switch",
            headers: apiAuthOptions().headers,
            data: apiAuthOptions().data
        }).then(function (result) {
            boton.hide();
            boton.siblings(".resolve").show();
            boton.siblings(".refuse").show();
            colaboracion.attr('style','border-color: #00aeef;border-width: medium');
            colaboracion.find(".nombre").text(result.data.nombre+' '+result.data.apellido1+' '+result.data.apellido2);

            $("#tab_pendiente").append(colaboracion.parent());

        });
    });
    $(".telefonico").on("click",function(event){
        event.preventDefault();
        $(this).attr("data-toggle","modal").attr("data-target", "#dialogo").attr("href","");
        id=$(this).parents(".profile_view").find(".fct").attr("id");
        list = $(this).parents(".profile_view").find(".listActivity");
        tipo = 'telefonico';
    });
    $(".book").on("click",function(event){
        event.preventDefault();
        $(this).attr("data-toggle","modal").attr("data-target", "#dialogo").attr("href","");
        col=$(this).parents(".profile_view").attr("id");
        list = $(this).parents(".profile_view").find(".listActivity");
        tipo = 'book';
    });
    $(".small").on("click",function(event){
        event.preventDefault();
        id=$(this).attr("id");
        $.ajax({
            method: "GET",
            url: "/api/activity/" + id ,
            headers: apiAuthOptions().headers,
            data: apiAuthOptions().data
        }).then(function (result) {
            $("#dialogo").find("#explicacion").val(result.data.comentari);
        }, function () {
            console.log("Error al buscarr");
        });
        $(this).attr("data-toggle","modal").attr("data-target", "#dialogo").attr("href","");
        tipo = 'seguimiento';
    });
    $("#formDialogo").on("submit", function(event){
        event.preventDefault();
        if (tipo === 'book') {
            var authBook = apiAuthOptions({explicacion: this.explicacion.value});
            $.ajax({
                method: "POST",
                url: "/api/colaboracion/" + col + "/book",
                headers: authBook.headers,
                data: authBook.data
            }).then(function (result) {
                texto = list.html();
                day = new Date;
                month = day.getMonth() + 1;
                texto = list.html() + "<small><a href='#' class='small dragable' id='"+result.data.id+"' draggable='draggable' data-toggle='modal' data-target='#dialogo'><em class='fa fa-plus'></em> " + day.getDate() + "/" + month + " <em class='fa fa-book'></em></a></small><br/>";
                list.html(texto);
                $("#dialogo").modal('hide');
            }, function () {
                console.log("Només es pot un per dia");
                $("#dialogo").modal('hide');
            });
        }
        if (tipo === 'telefonico') {
            var authTelefonico = apiAuthOptions({explicacion: this.explicacion.value});
            $.ajax({
                method: "POST",
                url: "/api/colaboracion/" + id + "/telefonico",
                headers: authTelefonico.headers,
                data: authTelefonico.data
            }).then(function (result) {
                texto = list.html();
                day = new Date;
                month = day.getMonth() + 1;
                texto = list.html() + "<small><a href='#' class='small dragable' id='"+result.data.id+"' draggable='draggable' data-toggle='modal' data-target='#dialogo'><em class='fa fa-plus'></em> " + day.getDate() + "/" + month + " <em class='fa fa-phone'></em></a></small><br/>";
                list.html(texto);
                $("#dialogo").modal('hide');
            }, function () {
                console.log("Només es pot un per dia");
                $("#dialogo").modal('hide');
            });
        }
        if (tipo === 'seguimiento'){
            var authSeguimiento = apiAuthOptions({comentari: this.explicacion.value});
            $.ajax({
                method: "PUT",
                url: "/api/activity/" + id ,
                headers: authSeguimiento.headers,
                data: authSeguimiento.data
            }).then(function () {
                $("#dialogo").modal('hide');
            }, function () {
                console.log("Error al modificar");
                $("#dialogo").modal('hide');
            });
        }
    });
    $('.fa-minus').on("click", function(event){
        event.preventDefault();
        event.stopPropagation();
        var id=$(this).parents(".small").attr("id");
        if (confirm('Vas a esborrar esta evidencia')) {
            var authDelete = apiAuthOptions();
            $.ajax({
                method: "DELETE",
                url: "/api/activity/" + id,
                dataType: 'json',
                headers: authDelete.headers,
                data: authDelete.data
            }).then(() => this.parentElement.remove());
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
            headers: apiAuthOptions().headers,
            data: apiAuthOptions().data
        }).then(function (result) {
                      instructor.empty(); // remove old options
                        $.each(result.data, function (key, value) {
                            instructor.append($("<option></option>")
                                .attr("value", value.dni).text(value.name+' '+value.surnames));
                        });

            }, function () {
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
            if (confirm('Vas a moure esta evidencia a una altra FCT')){
                var authMove = apiAuthOptions();
                $.ajax({
                    method: "GET",
                    url: "/api/activity/"+id+"/move/" + newFct.id ,
                    dataType: 'json',
                    headers: authMove.headers,
                    data: authMove.data
                }).then(function () {
                    newFct.querySelector('.listActivity').appendChild(document.getElementById(id).parentElement);
                }, function (result) {
                    alert("La sol·licitut no s'ha pogut completar: "+result.responseText);
                });
            }
        });
    });
})
