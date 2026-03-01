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

    $('.checkbox').on('change', function (event) {
        var idProfesor = $(this).prop('name');
        var idReunion = $(this).parent(1).parent(1).parent(1).parent(1).parent(1).prop('id');
        var asiste = $(this).prop("checked")?1:0;
        var auth = apiAuthOptions({
            idProfesor: idProfesor,
            idReunion: idReunion,
            asiste: asiste,
        });
        $.ajax({
            method: "PUT",
            url: "/api/asistencia/cambiar",
            headers: auth.headers,
            data: auth.data,
        }).then(function (res) {
            console.log(res)
        }, function (res) {
            if (asiste) {
                $(this).removeProp("checked");
            } else {
                $(this).prop("checked");
            }
            console.log(res)
        });
    })
});
