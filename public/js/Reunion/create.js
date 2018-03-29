'use strict';

$(function () {
    $("#tipo_id").change(function () {
        var tipo = $("#tipo_id").val();
        var token = $("#_token").text();
        $.ajax({
            method: 'GET',
            url: '/api/tiporeunion/' + tipo,
            data: {api_token: token},
        }).then(function (result) {
            if (result.data['select'] == 0)
                $('#grupo_id').prop('disabled', true);
            else {
                $('#grupo_id').prop('disabled', false);
            }
            if (result.data['numeracion']) {
                var newOptions = result.data['numeracion'];
                var $el = $("#numero_id");
                $el.empty(); // remove old options
                $.each(newOptions, function (key, value) {
                    $el.append($("<option></option>")
                        .attr("value", key).text(value));
                });
            }

        });
    });
    $("#fecha_id").on("dp.change", function () {
        if ($("#fecha_id").val() >= Date())
            $('#fichero_id').prop('disabled', true);
        else
            $('#fichero_id').prop('disabled', false);
    });
})