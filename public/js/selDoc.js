
$(".seleccion").on("click",function(event){
    event.preventDefault();
    $(this).attr("data-toggle", "modal").attr("data-target", "#A3").attr("href", "");
    var token = $("#_token").text();
    var url = $(this).attr("data-url");
    var route = $(this).attr("id");
    $('#formA3').attr("action",route);
    $.ajax({
        method: "GET",
        url: url,
        dataType: 'json',
        data: {api_token: token}
    })
        .then(function (result) {
            pintaTablaSeleccion(result.data,"#tableA3");
         }, function (result) {
            console.log("La solicitud no se ha podido completar.");
        });
});

$("#A3 .submit").click(function() {
    event.preventDefault();
    $("#checkall").prop('checked',false);
    $('#signatura').modal('hide');
    $(this).attr("data-toggle", "modal").attr("data-target", "#loading").attr("href", "");
    $("#formA3" ).submit();
});



