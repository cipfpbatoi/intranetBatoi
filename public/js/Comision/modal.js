'use strict';
$(function () {

    if ($("#kilometraje_id").val()==0)  $('#itinerario_id').prop('disabled',true);

    if ($('#fct_id').is(":checked")) {
        $('#field_servicio_id').attr('class','form-group item hidden');
        $('#field_alojamiento_id').attr('class','form-group item hidden');
        $('#field_comida_id').attr('class','form-group item hidden');
    }

    $("#kilometraje_id").change(function () {
        if ($('#kilometraje_id').val() == 0) {
            $('#itinerario_id').val('');
            $('#itinerario_id').prop('disabled',true);
        }
        else $('#itinerario_id').prop('disabled',false);
    });

    $("#fct_id").change(function () {
        if ($('#fct_id').is(":checked")){
            $('#servicio_id').val('Visita empreses FCT:');
            $('#alojamiento_id').val(0);
            $('#comida_id').val(0);
            $('#field_servicio_id').attr('class','form-group item hidden');
            $('#field_alojamiento_id').attr('class','form-group item hidden');
            $('#field_comida_id').attr('class','form-group item hidden');
        }
        else {
            $('#servicio_id').val('Visita empreses');
            $('#field_servicio_id').attr('class','form-group item');
            $('#field_alojamiento_id').attr('class','form-group item');
            $('#field_comida_id').attr('class','form-group item');
        }
    });

    let alertShown = false;

    $('#itinerario_id').attr('placeholder', "L'itinerari comença i acaba al centre");
    $("#kilometraje_id").focus(function () {
        if (!alertShown) {
            alert("Recorda que el quilometratge s'ha de comptar des del centre.");
            alertShown = true;
        }
    });

    
});
