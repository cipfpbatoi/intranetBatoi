'use strict';

$(function () {
    function apiAuthOptions(extraData) {
        var legacyToken = $.trim($("#_token").text());
        var bearerToken = $.trim($('meta[name="user-bearer-token"]').attr('content') || "");
        var data = extraData || {};
        var headers = {};

        if (bearerToken) {
            headers.Authorization = "Bearer " + bearerToken;
        } else if (legacyToken) {
            data.api_token = legacyToken;
        }

        return { headers: headers, data: data };
    }

    $('#sao_id').change(function () {
        var check = $("#sao_id").is(":checked");
        if (check){
            $('#concierto_id').prop('disabled', false);
        }
        else {
            $('#concierto_id').prop('disabled', true);
        }
    });
    $('#cif_id').change(function () {
        var cif = $("#cif_id").val();
        var auth = apiAuthOptions();
        $.ajax({
            method: "GET",
            url: "/api/Empresa/cif="+cif,
            dataType: 'json',
            headers: auth.headers,
            data: auth.data
        }).then(function (result) {
                alert("Error: CIF duplicat amb l'empresa "+result.data[0].nombre+' de concert '+result.data[0].concierto);
            });
    });
});
