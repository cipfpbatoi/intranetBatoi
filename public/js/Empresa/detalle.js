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
    $(".editar").click(function (event){
        event.preventDefault();
        let id= $(this).attr("id");
        let auth = apiAuthOptions();
        $('#AddColaboration').modal('show');
        //$(this).parents('a').attr("data-toggle", "modal").attr("data-target", "AddColaboration").attr("href", "");
        $.ajax({
            url: "/api/colaboracion/"+id,
            type: "GET",
            dataType: "json",
            headers: auth.headers,
            data: auth.data
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

    $(".centro").click(function (event){
        event.preventDefault();
        let id= $(this).attr("id");
        let auth = apiAuthOptions();
        $('#AddCenter').modal('show');
        //$(this).parents('a').attr("data-toggle", "modal").attr("data-target", "AddColaboration").attr("href", "");
        $.ajax({
            url: "/api/centro/"+id,
            type: "GET",
            dataType: "json",
            headers: auth.headers,
            data: auth.data
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

    $("#AddCenter button.submit.btn.btn-primary").click(function (event){
        if ($('#idCentro').val() != ''){
            event.preventDefault();
            let auth = apiAuthOptions({
                nombre: $('#nombreCentro').val(),
                telefono: $('#telefonoCentro').val(),
                email: $('#emailCentro').val(),
                direccion: $('#direccionCentro').val(),
                localidad: $('#localidadCentro').val(),
                observaciones: $('#observacionesCentro').val(),
                horarios: $('#horariosCentro').val(),
                codiPostal: $('#codiPostalCentro').val(),
                idioma: $('#idiomaCentro').val(),
            });
            $.ajax({
                url: "/api/centro/"+$('#idCentro').val(),
                type: "PUT",
                dataType: "json",
                headers: auth.headers,
                data: auth.data
            }).then(function (result) {
                $('#AddCenter').modal('hide');
                location.reload();
            }, function (error) {
                console.error(["Error " + error.status + ": " + error.statusText, "error"], 'error');
            });
        }
    });

    $("#AddColaboration button.submit.btn.btn-primary").click(function (event){
       if ($('#id').val() != ''){
           event.preventDefault();
           let auth = apiAuthOptions({
               contacto: $('#contacto_id').val(),
               telefono: $('#telefono').val(),
               email: $('#email').val(),
               puestos: $('#puestos').val(),
               tutor: $('#tutor').val()
           });
           $.ajax({
               url: "/api/colaboracion/"+$('#id').val(),
               type: "PUT",
               dataType: "json",
               headers: auth.headers,
               data: auth.data
           }).then(function (result) {
               $('#AddColaboration').modal('hide');
               location.reload();
           }, function (error) {
               showMessage(["Error " + error.status + ": " + error.statusText, "error"], 'error');
           });
       }
    });
    $("#fusionar").click(function (event) {
        event.preventDefault();
        let fusionar = [];
        $('input:checkbox').each(function () {
            if (this.checked)
                fusionar.push($(this).val());
        });
        let auth = apiAuthOptions({
            fusion: fusionar
        });
        $.ajax({
            url: "/api/centro/fusionar",
            type: "POST",
            dataType: "json",
            headers: auth.headers,
            data: auth.data
        }).then(function () {
            location.reload();
        }, function (error) {
            console.log(["Error " + error.status + ": " + error.statusText, "error"], 'error');
        });
    });
});


function editar(id) {
    $('#formAddEnterprise').attr('action','/centro/'+id+'/empresa/create');
    $('#AddEnterprise').modal({ show: true });

}
