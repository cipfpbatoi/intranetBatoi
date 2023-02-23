'use strict';

$(function () {
    if ($("#dni").text() === $("#creador_id").val() || ($('#rol').text() % 41 == 0) ||
        ($('#concierto_id').val() == '')) {
        if ($("#sao_id").is(":checked")) {
            $('#concierto_id').prop('disabled', false);
        } else {
            $('#concierto_id').prop('disabled', true);
        }
        $('#sao_id').change(function () {
            if ($("#sao_id").is(":checked")) {
                $('#concierto_id').prop('disabled', false);
            } else {
                $('#concierto_id').prop('disabled', true);
            }
        });
    } else {
        $('#concierto_id').hide();
        $('#europa_id').hide();
        $('#sao_id').hide();
    }

});