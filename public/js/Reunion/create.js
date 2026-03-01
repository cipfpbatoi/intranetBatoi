'use strict';

$(function () {
    function apiAuthOptions(extraData) {
        var legacyToken = $.trim($("#_token").text());
        var bearerToken = $.trim($('meta[name="user-bearer-token"]').attr('content') || "");
        var data = extraData || {};
        var headers = {};

        if (bearerToken) {
            headers.Authorization = "Bearer " + bearerToken;
        }
    if (legacyToken) {
            data.api_token = legacyToken;
        }

        return { headers: headers, data: data };
    }

    $("#tipo_id").change(function () {
        var tipo = $("#tipo_id").val();
        if (tipo == 9){
            $('#fichero_id').prop('disabled', false);
            $('#numero_id').prop('disabled', true);
            $('#descripcion_id').val('Acta FSE');
            $('#objetivos_id').prop('disabled', true);
            $('#grupo_id').prop('disabled', true);
        } else {
            $('#fichero_id').prop('disabled', true);
            $('#numero_id').prop('disabled', false);
            $('#objetivos_id').prop('disabled', false);
            $.ajax({
                method: 'GET',
                url: '/api/tiporeunion/' + tipo,
                headers: apiAuthOptions().headers,
                data: apiAuthOptions().data,
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
        }
    });
})
