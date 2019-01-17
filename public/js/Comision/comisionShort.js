'use strict';
$(function () {
    $('#servicio_id').prop('disabled',true);
    $('#alojamiento_id').prop('disabled',true);
    $('#comida_id').prop('disabled',true);
    $('#gastos_id').prop('disabled',true);
    $('#kilometraje_id').prop('disabled',true);
    $('#medio_id').prop('disabled',true);
    $('#marca_id').prop('disabled',true);
    $('#matricula_id').prop('disabled',true);
    $('#itinerario_id').prop('disabled',true);
    $('#otros_id').prop('disabled',true);
    
    $('#fct_id').change(function () {
        var check = $("#fct_id").is(":checked");
        if (check) {
            $('#servicio_id').prop('disabled',true);
            $('#alojamiento_id').prop('disabled',true);
            $('#comida_id').prop('disabled',true);
            $('#gastos_id').prop('disabled',true);
            $('#kilometraje_id').prop('disabled',true);
            $('#medio_id').prop('disabled',true);
            $('#marca_id').prop('disabled',true);
            $('#matricula_id').prop('disabled',true);
            $('#itinerario_id').prop('disabled',true);
            $('#otros_id').prop('disabled',true);
        } else {
            $('#servicio_id').prop('disabled',false);
            $('#alojamiento_id').prop('disabled',false);
            $('#comida_id').prop('disabled',false);
            $('#gastos_id').prop('disabled',false);
            $('#kilometraje_id').prop('disabled',false);
            $('#medio_id').prop('disabled',false);
            $('#marca_id').prop('disabled',false);
            $('#matricula_id').prop('disabled',false);
            $('#itinerario_id').prop('disabled',false);
            $('#otros_id').prop('disabled',false);
        }
    });
});
