
$(".selecciona").on("click",function(event){
    event.preventDefault();
    $(this).attr("data-toggle", "modal").attr("data-target", "#seleccion").attr("href", "");
    var token = $("#_token").text();
    var url = $(this).attr("data-url");
    var route = $(this).attr("id");
    $('#formSeleccion').attr("action",route);
    $.ajax({
        method: "GET",
        url: url,
        dataType: 'json',
        data: {api_token: token}
    })
        .then(function (result) {
            pintaTablaSeleccion(result.data);
         }, function (result) {
            console.log("La solicitud no se ha podido completar.");
        });
});

$("#seleccion .submit").click(function() {
    event.preventDefault();
    $("#checkall").prop('checked',false);
    $("#formSeleccion" ).submit();
});



