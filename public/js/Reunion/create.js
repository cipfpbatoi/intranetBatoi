'use strict';

$(function () {
    $("#tipo_id").change(function () {
        var tipo = $("#tipo_id").val();
        var token = $("#_token").text();
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
        }
    });
})