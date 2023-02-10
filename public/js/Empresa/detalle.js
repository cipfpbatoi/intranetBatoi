'use strict';

$(function () {
    $(".addCol").click(function (){
        let idCentro = $(this).attr('href');
        $('#idCentro').val(idCentro);
    })
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

    $(".centro").click(function (){
        event.preventDefault();
        let id= $(this).attr("id");
        let token = $("#_token").text();
        $('#AddCenter').modal('show');
        //$(this).parents('a').attr("data-toggle", "modal").attr("data-target", "AddColaboration").attr("href", "");
        $.ajax({
            url: "/api/centro/"+id,
            type: "GET",
            dataType: "json",
            data: {
                api_token: token,
            }
        }).then(function (result) {
            $('h4.modal-title').text('Modificar Centre Treball');
            $('#idCentro').val(result.data.id);
            $('#nombreCentro').val(result.data.nombre);
            $('#telefonoCentro').val(result.data.telefono);
            $('#emailCentro').val(result.data.email);
            $('#horariosCentro').val(result.data.horarios);
            $('#observacionesCentro').val(result.data.observaciones);
            $('#codiPostalCentro').val(result.data.codiPostal);
            $('#direccionCentro').val(result.data.direccion);
            $('#localidadCentro').val(result.data.localidad);
            $('#idiomaCentro').val(result.data.idioma);
        }, function (error) {
            showMessage(["Error " + error.status + ": " + error.statusText, "error"], 'error');
        });
        console.log(id);
    });

    $("#AddCenter button.submit.btn.btn-primary").click(function (){
        if ($('#idCentro').val() != ''){
            event.preventDefault();
            let token = $("#_token").text();
            $.ajax({
                url: "/api/centro/"+$('#idCentro').val(),
                type: "PUT",
                dataType: "json",
                data: {
                    api_token: token,
                    nombre: $('#nombreCentro').val(),
                    telefono: $('#telefonoCentro').val(),
                    email: $('#emailCentro').val(),
                    direccion: $('#direccionCentro').val(),
                    localidad: $('#localidadCentro').val(),
                    observaciones: $('#observacionesCentro').val(),
                    horarios: $('#horariosCentro').val(),
                    codiPostal: $('#codiPostalCentro').val(),
                    idioma: $('#idiomaCentro').val(),
                }
            }).then(function (result) {
                $('#AddCenter').modal('hide');
                location.reload();
            }, function (error) {
                console.error(["Error " + error.status + ": " + error.statusText, "error"], 'error');
            });
        }
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
    $('#formAddEnterprise').attr('action','/centro/'+id+'/empresa/create');
    $('#AddEnterprise').modal({ show: true });

}

