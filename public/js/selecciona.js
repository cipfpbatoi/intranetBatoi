$(".selecciona").on("click",function(event){
    event.preventDefault();
    $(this).attr("data-toggle", "modal").attr("data-target", "#seleccion").attr("href", "");
    var token = $("#_token").text();
    var url = $(this).attr("data-url");
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
function pintaTablaSeleccion(newOptions){
    var $el = $("#tableSeleccion");
    $el.empty(); // remove old options
    $.each(newOptions, function (key, value) {
        $el.append($("<tr><td><input type='checkbox' name='"+value.id+"' checked> "+value.texto+"</td></tr>"));
    });
}
