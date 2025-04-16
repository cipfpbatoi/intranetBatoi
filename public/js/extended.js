$(".selecciona").on("click",function(event) {
    event.preventDefault();
    $(this).attr("data-toggle", "modal").attr("data-target", "#seleccion").attr("href", "");
    var token = $("#_token").text();
    var url = '/api/documentacionFCT/pg0301';
    $('#formSeleccion').attr("action",url.substring(4));
});
$("#informe").change(function () {
    var token = $("#_token").text();
    var url = '/api/documentacionFCT/'+$(this).val();
    $('#formSeleccion').attr("action",url.substring(4));
    $.ajax({
        method: "GET",
        url: url,
        dataType: 'json',
        data: {api_token: token}
    })
        .then(function (result) {
            pintaTablaSeleccion(result.data,"#tableSeleccion");
         }, function (result) {
            console.log("La solicitud no se ha podido completar.");
        });
});

$("#seleccion .submit").click(function() {
    event.preventDefault();
    $("#checkall").prop('checked',false);
    $("#formSeleccion" ).submit();
});
