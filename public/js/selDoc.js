
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

$(".seleccion").on("click",function(event){
    event.preventDefault();
    $(this).attr("data-toggle", "modal").attr("data-target", "#A3A").attr("href", "");
    var url = $(this).attr("data-url");
    var route = $(this).attr("id");
    $('#formA3A').attr("action",route);
    var auth = apiAuthOptions();
    $.ajax({
        method: "GET",
        url: url,
        dataType: 'json',
        headers: auth.headers,
        data: auth.data
    })
        .then(function (result) {
            pintaTablaSeleccion(result.data,"#tableA3");
         }, function (result) {
            console.log("La solicitud no se ha podido completar.");
        });
});

$("#A3A .submit").click(function(event) {
    event.preventDefault();
    $("#checkall").prop('checked',false);
    $('#signatura').modal('hide');
    $(this).attr("data-toggle", "modal").attr("data-target", "#loading").attr("href", "");
    $("#formA3A" ).submit();
});


