'use strict';

var id;
var col;
var list;
var day;
var month;
var tipo;

function bindDraggableLink(link) {
    if (!link) {
        return;
    }
    link.setAttribute('draggable', 'draggable');
    link.addEventListener('dragstart', function (event) {
        event.dataTransfer.setData('text/plain', event.target.id);
    });
}

function appendActivityLink(listTarget, activityId, iconClass, comment) {
    day = new Date();
    month = day.getMonth() + 1;
    var hasComment = $.trim(comment || '').length > 0;
    var stateIcon = hasComment ? 'plus' : 'minus';
    var safeComment = $('<div>').text(comment || '').html();

    var html = "<small><a href='#' class='small dragable' id='" + activityId +
        "' draggable='draggable' data-toggle='modal' data-target='#dialogo' title='" + safeComment + "'>" +
        "<em class='fa fa-" + stateIcon + "'></em> " + day.getDate() + "/" + month +
        " <em class='fa fa-" + iconClass + "'></em></a></small><br/>";

    listTarget.append(html);
    bindDraggableLink(document.getElementById(String(activityId)));
}

function apiAuthOptions(extraData) {
    var legacyToken = $.trim($("#_token").text());
    var bearerToken = $.trim($('meta[name="user-bearer-token"]').attr('content') || "");
    var csrfToken = $.trim($('meta[name="csrf-token"]').attr('content') || "");
    var data = extraData || {};
    var headers = {};

    if (csrfToken) {
        headers['X-CSRF-TOKEN'] = csrfToken;
    }

    if (bearerToken) {
        headers.Authorization = "Bearer " + bearerToken;
    }
    if (legacyToken) {
        data.api_token = legacyToken;
    }

    return { headers: headers, data: data };
}

$(function() {
    $(document).on("click", ".telefonico", function (event) {
        event.preventDefault();
        id=$(this).parents(".profile_view").find(".fct").attr("id");
        list = $(this).parents(".profile_view").find(".listActivity");
        tipo = 'telefonico';
        $("#dialogo").find("#explicacion").val('');
        $("#dialogo").modal('show');

        var todayPhone = list.find("a.small").filter(function () {
            return $(this).find("em.fa-phone").length > 0;
        }).last();

        if (todayPhone.length) {
            var authTodayPhone = apiAuthOptions();
            $.ajax({
                method: "GET",
                url: "/activity/" + todayPhone.attr("id"),
                headers: authTodayPhone.headers,
                data: authTodayPhone.data
            }).then(function (result) {
                $("#dialogo").find("#explicacion").val((result.data && result.data.comentari) || "");
            }, function () {
                console.log("No s'ha pogut carregar el comentari telefonic existent.");
            });
        }
    });

    $(document).on("click", ".listActivity a.small", function (event) {
        event.preventDefault();
        id=$(this).attr("id");
        tipo = 'seguimiento';
        $("#dialogo").find("#explicacion").val('');
        $("#dialogo").modal('show');
        var authShow = apiAuthOptions();
        $.ajax({
            method: "GET",
            url: "/activity/" + id ,
            headers: authShow.headers,
            data: authShow.data
        }).then(function (result) {
            $("#dialogo").find("#explicacion").val(result.data.comentari);
        }, function () {
            console.log("Error al buscar");
        });
    });
    $("#formDialogo").on("submit", function(event){
        event.preventDefault();
        if (tipo === 'telefonico') {
            var comentariTelefonic = this.explicacion.value;
            var authTelefonico = apiAuthOptions({explicacion: comentariTelefonic});
            $.ajax({
                method: "POST",
                url: "/colaboracion/" + id + "/telefonico",
                headers: authTelefonico.headers,
                data: authTelefonico.data
            }).then(function (result) {
                var targetId = String(result.data.id);
                var existing = list.find("a.small#" + targetId);
                if (existing.length) {
                    var hasComment = $.trim(comentariTelefonic).length > 0;
                    existing.find("em.fa").first()
                        .toggleClass("fa-plus", hasComment)
                        .toggleClass("fa-minus", !hasComment);
                    existing.attr("title", comentariTelefonic)
                        .attr("data-original-title", comentariTelefonic);
                } else {
                    appendActivityLink(list, result.data.id, 'phone', comentariTelefonic);
                }
                $("#dialogo").modal('hide');
            }, function () {
                console.log("Només es pot un per dia");
                $("#dialogo").modal('hide');
            });
        }
        if (tipo === 'seguimiento'){
            var comentariActualitzat = this.explicacion.value;
            var authSeguimiento = apiAuthOptions({comentari: comentariActualitzat});
            $.ajax({
                method: "PUT",
                url: "/activity/" + id ,
                headers: authSeguimiento.headers,
                data: authSeguimiento.data
            }).then(function () {
                var link = $("#" + id);
                var stateIcon = link.find("em.fa").first();
                var hasComment = $.trim(comentariActualitzat).length > 0;

                stateIcon
                    .toggleClass("fa-plus", hasComment)
                    .toggleClass("fa-minus", !hasComment);

                link
                    .attr("title", comentariActualitzat)
                    .attr("data-original-title", comentariActualitzat);
                $("#dialogo").modal('hide');
            }, function () {
                console.log("Error al modificar");
                $("#dialogo").modal('hide');
            });
        }
    });

    $(document).on("click", ".listActivity a.small .fa-minus", function (event) {
        event.preventDefault();
        event.stopPropagation();
        var id=$(this).closest("a.small").attr("id");
        if (confirm('Vas a esborrar esta evidencia')) {
            var authDelete = apiAuthOptions();
            $.ajax({
                method: "DELETE",
                url: "/activity/" + id,
                dataType: 'json',
                headers: authDelete.headers,
                data: authDelete.data
            }).then(() => {
                var small = $(this).closest('small');
                small.next('br').remove();
                small.remove();
            });
        }
    });

    $(document).on("click", ".profile_view .bottom .fa-plus", function () {
        if ($(this).closest(".listActivity").length) {
            return;
        }
        var id=$(this).parents(".profile_view").attr("id");
        var instructor = $("#idInstructor");
        $('#formAddAlumno').attr('action', '/fct/fctalumnoCreate');
        $('#idColaboracion').attr('value',id);
        var authInstructores = apiAuthOptions();
        $.ajax({
            method: "GET",
            url: "/colaboracion/instructores/" + id ,
            dataType: 'json',
            headers: authInstructores.headers,
            data: authInstructores.data
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
    var pageLocale = (($('meta[name="app-locale"]').attr('content') || $('html').attr('lang') || 'es').toLowerCase()).split('-')[0];
    var pickerLocale = pageLocale === 'en' ? 'en' : (pageLocale === 'ca' ? 'ca' : 'es');
    var dateFormat = pageLocale === 'en' ? 'MM/DD/YYYY' : 'DD/MM/YYYY';
    var dateTimeFormat = pageLocale === 'en' ? 'MM/DD/YYYY h:mm A' : 'DD/MM/YYYY HH:mm';

    if (typeof moment !== 'undefined' && typeof moment.locale === 'function') {
        moment.locale(pickerLocale);
    }

    $('input[type=text].datetime').datetimepicker({
        sideBySide: true,
        locale: pickerLocale,
        format: dateTimeFormat,
        stepping: 15,
    });
    $('input[type=text].time').datetimepicker({
        sideBySide: true,
        locale: pickerLocale,
        format: 'HH:mm',
        stepping: 15,
    });
    $('input[type=text].date').datetimepicker({
        sideBySide: true,
        locale: pickerLocale,
        format: dateFormat,
    });
    Array.from(document.querySelectorAll('.dragable')).forEach((item) => {
        bindDraggableLink(item);
    });
    Array.from(document.querySelectorAll('.fct')).forEach((item)=>{
        item.addEventListener('dragover',(event)=>{
            event.preventDefault();
        });
        item.addEventListener('drop',(event)=>{
            event.preventDefault();
            let id = event.dataTransfer.getData('text/plain');
            let newFct = event.currentTarget;
            const node = document.getElementById(id).parentElement;
            const nodeCopy = node.cloneNode(true);
            if (confirm('Vas a copiar esta evidencia a una altra FCT')){
                var authMove = apiAuthOptions();
                $.ajax({
                    method: "GET",
                    url: "/activity/"+id+"/move/" + newFct.id ,
                    dataType: 'json',
                    headers: authMove.headers,
                    data: authMove.data
                }).then(function (result) {
                    nodeCopy.firstElementChild.id = result.data.id;
                    nodeCopy.firstElementChild.firstElementChild.classList.remove('fa-plus');
                    nodeCopy.firstElementChild.firstElementChild.classList.add('fa-minus');
                    newFct.querySelector('.listActivity').appendChild(nodeCopy);
                    location.reload();
                }, function (result) {
                    alert("La sol·licitut no s'ha pogut completar: "+result.responseText);
                });
            }
        });
    });
})
