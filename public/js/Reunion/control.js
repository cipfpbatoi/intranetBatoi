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
    
})