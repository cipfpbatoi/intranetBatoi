$("#btn-selecciona").on("click",function(event){
    event.preventDefault();
    $(this).attr("data-toggle", "modal").attr("data-target", "#seleccion").attr("href", "");
    var token = $("#_token").text();
    var dni = $("#dni").text();
    $.ajax({
        method: "GET",
        url: "/api/alumnoFct/"+dni+"/misAlumnos",
        dataType: 'json',
        data: {api_token: token}
    })
        .then(function (result) {
            var newOptions = result.data;
            var $el = $("#tableSeleccion");
            $el.empty(); // remove old options
            $.each(newOptions, function (key, value) {
                $el.append($("<tr><td><input type='checkbox' name='"+value.id+"' checked> "+value.texto+"</td></tr>"));
            });
        }, function (result) {
            console.log("La solicitud no se ha podido completar.");
        });
});
