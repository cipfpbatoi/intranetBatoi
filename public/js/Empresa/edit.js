'use strict';

$(function () {
    if ($("#dni").text() === $("#creador_id").val() || ($('#rol').text() % 2 == 0) ||
        ($('#concierto_id').val() == '')) {
        if ($("#europa_id").is(":checked")) {
            $('#sao_id').prop('disabled', true);
            $('#concierto_id').prop('disabled', true);
        } else {
            $('#sao_id').prop('disabled', false);
        }
        $('#europa_id').change(function () {
            if ($("#europa_id").is(":checked")) {
                $('#sao_id').prop('disabled', true);
                $('#concierto_id').prop('disabled', true);
            } else {
                $('#sao_id').prop('disabled', false);
                if ($("#sao_id").is(":checked")) {
                    $('#concierto_id').prop('disabled', false);
                } else {
                    $('#concierto_id').prop('disabled', true);
                }
            }
        });
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
        $('#concierto_id').prop('disabled', true);
        $('#europa_id').prop('disabled', true);
        $('#sao_id').prop('disabled', true);
    }

});