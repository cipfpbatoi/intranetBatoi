'use strict';
$(function () {
    
    var kilometraje = $('#kilometraje_id').val();
    if (kilometraje == 0) $('#itinerario_id').prop('disabled',true);
    
    $("#kilometraje_id").change(function () {
        kilometraje = $('#kilometraje_id').val();
        if (kilometraje == 0) $('#itinerario_id').prop('disabled',true);
        else $('#itinerario_id').prop('disabled',false);
    });
    
});
