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
