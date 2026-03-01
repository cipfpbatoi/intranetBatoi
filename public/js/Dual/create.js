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

$(function () {

    $('#idColaboracion_id').change(function () {
        var idColaboracion = $("#idColaboracion_id").val();
        var auth = apiAuthOptions();
        $.ajax({
            method: "GET",
            url: "/api/colaboracion/instructores/" + idColaboracion,
            dataType: 'json',
            headers: auth.headers,
            data: auth.data
        })
            .then(function (result) {
                var newOptions = result.data;
                var $el = $("#idInstructor_id");
                $el.empty(); // remove old options
                $.each(newOptions, function (key, value) {
                    $el.append($("<option></option>")
                        .attr("value", value.dni).text(value.name+' '+value.surnames));
                });
            }, function (result) {
                console.log("La solicitud no se ha podido completar.");
            });
    });

});

function postModal() {
    var idColaboracion = $("#idColaboracion_id").val();
    var idInstructor = $("#idInstructor_id").val();
    if (idColaboracion) {
        var auth = apiAuthOptions();
        $.ajax({
            method: "GET",
            url: "/api/colaboracion/instructores/" + idColaboracion,
            dataType: 'json',
            headers: auth.headers,
            data: auth.data
        })
            .then(function (result) {
                var newOptions = result.data;
                var $el = $("#idInstructor_id");
                $el.empty(); // remove old options
                $.each(newOptions, function (key, value) {
                    if (idInstructor == value.dni){
                        $el.append($("<option selected></option>")
                            .attr("value", value.dni).text(value.name + ' ' + value.surnames));
                    } else {
                        $el.append($("<option></option>")
                            .attr("value", value.dni).text(value.name + ' ' + value.surnames));
                    }
                });
            }, function (result) {
                console.log("La solicitud no se ha podido completar.");
            });
    }
}
