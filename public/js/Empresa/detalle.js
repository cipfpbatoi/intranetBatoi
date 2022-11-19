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
    })
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

