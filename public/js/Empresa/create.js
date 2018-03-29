'use strict';

$(function () {
    $('#europa_id').change(function () {
        var check = $("#europa_id").is(":checked");
        if (check){
            $('#sao_id').prop('disabled', true);
            $('#concierto_id').prop('disabled', true);
        }
        else {
            $('#sao_id').prop('disabled', false);
            var check = $("#sao_id").is(":checked");
            if (check){
                $('#concierto_id').prop('disabled', false);
            }
            else {
                $('#concierto_id').prop('disabled', true);
            }
        }
    });
    $('#sao_id').change(function () {
        var check = $("#sao_id").is(":checked");
        if (check){
            $('#concierto_id').prop('disabled', false);
        }
        else {
            $('#concierto_id').prop('disabled', true);
        }
    });
});