'use strict';

$(function () {
    $("#contacto_id").change(function () {
        let contacto = $('#contacto_id').val();
        if ($('#instructor_id').val() === '') {
            $('#instructor_id').empty().val(contacto);
        }
    });
    $("input.btn-sm.btn-danger").click(function () {
        confirm('Vas a crear una nova empresa a partir del centre de treball')
    });
    $(".editar").click(function (){
        event.preventDefault();
        let id= $(this).attr("id");
        let token = $("#_token").text();
        $('#AddColaboration').modal('show');
        //$(this).parents('a').attr("data-toggle", "modal").attr("data-target", "AddColaboration").attr("href", "");
        $.ajax({
            url: "/api/colaboracion/"+id,
            type: "GET",
            dataType: "json",
            data: {
                api_token: token,
            }
        }).then(function (result) {
            $('h4.modal-title').text('Modificar Col·laboracio');
            $('#idCiclo').val(result.data.idCiclo).prop('disabled', 'disabled');
            $('#idCentro').val(result.data.idCentro).prop('disabled', 'disabled');
            $('#id').val(result.data.id);
            $('#contacto_id').val(result.data.contacto);
            $('#telefono').val(result.data.telefono);
            $('#email').val(result.data.email);
            $('#tutor').val(result.data.tutor);
            $('#puestos').val(result.data.puestos);
        }, function (error) {
            showMessage(["Error " + error.status + ": " + error.statusText, "error"], 'error');
        });
        console.log(id);
    });
    $("#AddColaboration button.submit.btn.btn-primary").click(function (){
       if ($('#id').val() != ''){
           event.preventDefault();
           let token = $("#_token").text();
           $.ajax({
               url: "/api/colaboracion/"+$('#id').val(),
               type: "PUT",
               dataType: "json",
               data: {
                   api_token: token,
                   contacto: $('#contacto_id').val(),
                   telefono: $('#telefono').val(),
                   email: $('#email').val(),
                   puestos: $('#puestos').val(),
                   tutor: $('#tutor').val()
               }
           }).then(function (result) {
               $('#AddColaboration').modal('hide');
               location.reload();
           }, function (error) {
               showMessage(["Error " + error.status + ": " + error.statusText, "error"], 'error');
           });
       }
    });
    $("#fusionar").click(function () {
        let fusionar = [];
        let token = $("#_token").text();
        $('input:checkbox').each(function () {
            if (this.checked)
                fusionar.push($(this).val());
        });
        $.ajax({
            url: "/api/centro/fusionar",
            type: "POST",
            dataType: "json",
            data: {
                api_token: token,
                fusion: fusionar
            }
        }).then(function () {
            location.reload();
        }, function (error) {
            showMessage(["Error " + error.status + ": " + error.statusText, "error"], 'error');
        });
    });
});


function editar(id) {
    $('#formEnterprise').attr('action','/centro/'+id+'/empresa/create');
    $('#AddEnterprise').modal({ show: true });

}

