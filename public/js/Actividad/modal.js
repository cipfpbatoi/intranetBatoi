'use strict';
$(function () {
    $("#fueraCentro_id").change(function () {
        if (!$('#fueraCentro_id').is(":checked")) {
            $('#transport_id').prop('checked','');
            $('#transport_id').prop('disabled',true);
        }
        else $('#transport_id').prop('disabled',false);
    });
});
