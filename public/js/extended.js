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

$(".selecciona").on("click",function(event) {
    event.preventDefault();
    $(this).attr("data-toggle", "modal").attr("data-target", "#seleccion").attr("href", "");
    var url = '/api/documentacionFCT/pg0301';
    $('#formSeleccion').attr("action",url.substring(4));
});
$("#informe").change(function () {
    var url = '/api/documentacionFCT/'+$(this).val();
    $('#formSeleccion').attr("action",url.substring(4));
    var auth = apiAuthOptions();
    $.ajax({
        method: "GET",
        url: url,
        dataType: 'json',
        headers: auth.headers,
        data: auth.data
    })
        .then(function (result) {
            pintaTablaSeleccion(result.data,"#tableSeleccion");
         }, function (result) {
            console.log("La solicitud no se ha podido completar.");
        });
});

$("#seleccion .submit").click(function(event) {
    event.preventDefault();
    $("#checkall").prop('checked',false);
    $("#formSeleccion" ).submit();
});
