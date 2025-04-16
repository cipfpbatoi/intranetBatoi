
$(".selecciona").on("click",function(event){
    event.preventDefault();
    $(this).attr("data-toggle", "modal").attr("data-target", "#seleccion").attr("href", "");
    var token = $("#_token").text();
    var groupId = $(this).attr("id").replace("list", ""); // Extreu l'ID després de "list"
    var url = '/api/grupo/list/' + groupId; // Crea la nova URL amb l'ID de grup

    $('#formSeleccion').attr("action",'/grupo/list');
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



