'use strict';

$(function () {
    $('#grupo_id').prop('disabled', true);
    $('#tipoDocumento_id').change(function () {
        var tipo = $("#tipoDocumento_id").val();
        if (tipo == 'Acta'){
            $('#grupo_id').prop('disabled', false);
        }
        else {
            $('#grupo_id').prop('disabled', true);
            $('#grupo_id').val('');
        $('#grupo_id').prop('disabled', true);
    }
    });
    $('#nota_id').change(function () {
        var nota = $("#nota_id").val();
        if (nota < 5){
            $('#fichero_id').prop('disabled', true);
        }
        else {
            $('#fichero_id').prop('disabled', false);
        }
    });
})