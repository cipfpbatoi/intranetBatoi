'use strict';

$(function () {
    $('#sao_id').change(function () {
        var check = $("#sao_id").is(":checked");
        if (check){
            $('#concierto_id').prop('disabled', false);
        }
        else {
            $('#concierto_id').prop('disabled', true);
        }
    });
    $('#cif_id').change(function () {
        var cif = $("#cif_id").val();
        var token = $("#_token").text();
        $.ajax({
            method: "GET",
            url: "/api/Empresa/cif="+cif,
            dataType: 'json',
            data: {api_token: token}
        }).then(function (result) {
                alert("Error: CIF duplicat amb l'empresa "+result.data[0].nombre+' de concert '+result.data[0].concierto);
            });
    });
});