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

    $('#dni_id').change(function () {
        var dni = $("#dni_id").val();
        var auth = apiAuthOptions();
        $.ajax({
            method: "GET",
            url: "/api/instructor/"+dni,
            headers: auth.headers,
            data: auth.data,
        })
        .then(function (result) {
            $('#name_id').val(result.data.name);
            $('#surnames_id').val(result.data.surnames);
            $('#email_id').val(result.data.email);
            $('#telefono_id').val(result.data.telefono);
        }, function (result) {
            console.log("La solicitud no se ha podido completar.");
        });
    });
});
