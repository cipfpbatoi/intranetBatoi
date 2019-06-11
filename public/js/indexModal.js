'use strict'

$(function () {
    var modelo = $("#datatable").attr('name').toLowerCase();
    var formModal = $('.modal form');
    var token = $("#_token").text();
    
    $('#create').on('hidden.bs.modal', function () {
        console.log('hola');// do something…
        var id=$(this).find('#id').val();
        if (id) {
            $(id).find('.fa-edit').parents('a').attr("href", jQuery(location).attr('href')+"/"+id+"/edit");
        }
    })
    $(".txtButton").on("click", function () {
        event.preventDefault();
        $('.form-horizontal')[0].reset();
        formModal.attr('action',jQuery(location).attr('href'));
        $('#metodo').val('POST');
        $(this).attr("data-toggle", "modal").attr("data-target", "#create").attr("href", "");
    });
    $(".fa-edit").on("click", function () {
        event.preventDefault();
//        var hrefBtn = $(this).parents('a').attr('href');
        var id = $(this).parents('tr').attr('id');
        $(this).parents('a').attr("data-toggle", "modal").attr("data-target", "#create").attr("href", "");
            //.attr("href", "");
        $.ajax({
            method: "GET",
            url: "/api/" + modelo + "/" + id,
            dataType: 'json',
            data: {api_token: token},
        }).then(function (res) {
            formModal.attr('action', jQuery(location).attr('href')+"/"+id+"/edit");
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
    if ($('div .alert-danger').length) {
        if ($('#id').val() > 0){
           var formModal = $('.modal form');
           var href = formModal.attr('action')+'/'+$('#id').val()+'/edit';
           formModal.attr('action',href);
        }
        $('#create').modal('show');
        
    }
})

