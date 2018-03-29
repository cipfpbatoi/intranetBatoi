'use strict';

$(function () {
    $('#hora_ini_id').prop('disabled', true);
    $('#hora_fin_id').prop('disabled', true);
    $("#dia_completo_id").change(function () {
        var tipo = $('#dia_completo_id').is(":checked");
        if (tipo == 1){
            $('#hora_ini_id').prop('disabled', true);
            $('#hora_fin_id').prop('disabled', true);
        }
        else{
            $('#hora_ini_id').prop('disabled', false);
            $('#hora_fin_id').prop('disabled', false);
        }
    });
    $("#baja_id").change(function () {
        var tipo = $('#baja_id').is(":checked");
        if (tipo == 1){
            $('#hora_ini_id').prop('disabled', true);
            $('#hora_fin_id').prop('disabled', true);
            $('#hasta_id').prop('disabled', true);
            $('#dia_completo_id').prop('disabled', true);
        }
        else{
            $('#hasta_id').prop('disabled', false);
            $('#dia_completo_id').prop('disabled', false);
            var dia = $('#dia_completo_id').is(":checked");
            if (dia == 0){
                $('#hora_ini_id').prop('disabled', false);
                $('#hora_fin_id').prop('disabled', false);
            }
        }
    });
})