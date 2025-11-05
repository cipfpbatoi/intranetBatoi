'use strict'
var modelo = $("#datatable").attr('name').toLowerCase();
var formModal = $('.modal form');
var token = $("#_token").text();
$(function () {
    $('#create').on('hidden.bs.modal', function () {
        var id=$(this).find('#id').val();
        if (id) {
            $('#'.id).find('.fa-edit').parents('a').attr("href", jQuery(location).attr('href')+"/"+id+"/edit");
        }
    })
    $("a.btn-primary.btn.txtButton").on("click", function (e) {
        e.preventDefault();
        var formModal = $('.modal form');
        $('#id').val('');
        $('.form-horizontal')[0].reset();
        formModal.attr('action',jQuery(location).attr('href').replace(/#/,""));
        $('#metodo').val('POST');
        $(this).attr("data-toggle", "modal").attr("data-target", "#create").attr("href", "");
    });

    if ($('div .alert-danger').length) {
        if ($('#id').val() > 0){
           var formModal = $('.modal form');
           var href = formModal.attr('action')+'/'+$('#id').val()+'/edit';
           formModal.attr('action',href);
        }
        let cur_modal = localStorage.getItem("cur_modal");
        if (!cur_modal) {
            cur_modal = '#create';
        } else {
            localStorage.removeItem("cur_modal");
        }
        $(cur_modal).modal('show');
    }
    //  Barcode
    $('#datatable').on('click', 'a.QR', function (event) {
        let url = $(this).prop('href') + '/';
        event.preventDefault();
        var posicion = window.prompt("Introdueix posició de la primera etiqueta", 1);
        url += posicion;
        $(location).attr('href', url);
    });
})

jQuery(document).on('auxclick', '.fa-edit', function (e) {
    if (e.which === 2) { //middle Click
        return false;
    }
    return true;
});
jQuery(document).ready(function() {
    // Temporalment deshabilitar enllaços amb la classe `.fa-edit` afegint-los una classe `disabled-link`
    jQuery('.fa-edit').addClass('disabled-link').on('click', function(e) {
        // Prevenir l'acció per defecte dels enllaços si tenen la classe `disabled-link`
        if (jQuery(this).hasClass('disabled-link')) {
            e.preventDefault();
            return false;
        }
    });
    jQuery(".fa-edit").on("contextmenu",function(e){
        return false;
    });
    // Una vegada la pàgina estigui completament carregada, reactivar els enllaços eliminant la classe `disabled-link`
    jQuery(window).on('load', function() {
        jQuery('.fa-edit').removeClass('disabled-link');
    });
});



jQuery("#datatable").on("click",".fa-edit" ,function (e) {
    e.preventDefault();
    var id = $(this).parents('tr').attr('id');
    $(this).parents('a').attr("data-toggle", "modal").attr("data-target", "#create").attr("href", "");
    $.ajax({
        method: "GET",
        url: "/api/" + modelo + "/" + id + "/edit",
        dataType: 'json',
        data: {api_token: token},
    }).then(function (res) {
        formModal.attr('action', jQuery(location).attr('href').replace(/#/,"")+"/"+id+"/edit");
        formModal.find('#metodo').val('PUT').end().find('#id').val(id);
        var primerElem = "";
        for (var propiedad in res.data) {
            var elem = $('#' + propiedad + '_id');
            if (elem.length > 0) {
                // El campo existe en el formulario
                if (!primerElem)
                    primerElem = propiedad;
                if (elem[0].tagName.toUpperCase() == "INPUT" && elem.attr('type').toUpperCase() == "CHECKBOX") {
                    elem.prop('checked', res.data[propiedad]);
                } else {
                    if (elem[0].tagName.toUpperCase() == "INPUT" && elem.attr('type').toUpperCase()=='FILE'){
                        $("[id='Fichero Actual']").text(res.data[propiedad]);
                    }
                    else elem.val(res.data[propiedad]);
                }
                if (res.data[propiedad] != '')
                    elem.focus();
            }
        }
        if (typeof (postModal) == 'function')
            postModal();
        $('#'+primerElem+ '_id').focus();
    }, function (error) {
        console.log(error);
    })
});

jQuery("#datatable").on("click",".fa-eye" ,function (e) {
    e.preventDefault();
    var id = $(this).parents('tr').attr('id');
    $(this).parents('a').attr("data-toggle", "modal").attr("data-target", "#show").attr("href", "");
    $.ajax({
        method: "GET",
        url: "/api/" + modelo + "/" + id ,
        dataType: 'json',
        data: {api_token: token},
    }).then(function (res) {
        var html = '<ul class="to_do">';
        for (var propiedad in res.data) {
            if (propiedad === 'fichero' && res.data[propiedad]!=null)
                html += "<li><img src='storage/"+res.data[propiedad]+"' height='400' width='300'/>'</li>";
            else
                html += "<li><strong style='text-transform: capitalize'>"+propiedad+"</strong>: "+res.data[propiedad]+"</li>";
        }
        $("#campos").html(html);
    });
});