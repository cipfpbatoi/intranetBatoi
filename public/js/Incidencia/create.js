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

    $('#espacio_id').change(function () {
        var espacio = $("#espacio_id").val();
        var auth = apiAuthOptions();
        $.ajax({
            method: "GET",
            url: "/api/material/espacio/" + espacio,
            headers: auth.headers,
            data: auth.data,
        })
            .then(function (result) {
                $("#material_id").empty().append("<option value=0>Escoge un material</option>")
                $(result).each(function (i, item) {
                    $("#material_id").append("<option value='" + item.id + "'>" + item.descripcion + '('+ item.id +')' +"</option>");
                });
            }, function (result) {
                console.log("La solicitud no se ha podido completar.");
            });
    });
    $('#material_id').change(function () {
        var idMaterial = $("#material_id").val();
        $("#descripcion_id").empty().val($("#material_id").find("option:selected").text());
    });
    $('#tipo_id').change(function () {
        var tipo = $("#tipo_id").val();
        var auth = apiAuthOptions();
        $.ajax({
            method: "GET",
            url: "/api/tipoincidencia/" + tipo,
            headers: auth.headers,
            data: auth.data,
        }).then(function (result) {
            if (result.data.tipus == 2) {
                $('#espacio_id').prop('disabled', true);
                $('#material_id').prop('disabled', true);
            } else {
                $('#espacio_id').prop('enabled', true);
                $('#material_id').prop('enabled', true);
            }
        });
    });
});

