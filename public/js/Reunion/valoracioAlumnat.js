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

    $('.valoraciones').on('change', function (event) {
        var idAlumno = $(this).parent(1).siblings().first().text();
        var idReunion = $(this).parent(1).parent(1).parent(1).parent(1).parent(1).prop('id');
        var capacitats = $(this).val();
        var auth = apiAuthOptions({
            capacitats: capacitats,
        });
        $.ajax({
            method: "PUT",
            url: "/api/reunion/"+idReunion+"/alumno/"+idAlumno,
            headers: auth.headers,
            data: auth.data,
        }).then(function (res) {
            console.log(res)
        }, function (res) {
            console.log(res)
        });
    })
});
