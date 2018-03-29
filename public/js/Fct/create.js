$(function () {

    $('#idColaboracion_id').change(function () {
        var idColaboracion = $("#idColaboracion_id").val();
        var token = $("#_token").text();
        $.ajax({
            method: "GET",
            url: "/api/colaboracion/instructores/" + idColaboracion,
            dataType: 'json',
            data: {api_token: token}
        })
            .then(function (result) {
                var newOptions = result.data;
                var $el = $("#idInstructor_id");
                $el.empty(); // remove old options
                $.each(newOptions, function (key, value) {
                    $el.append($("<option></option>")
                        .attr("value", value.dni).text(value.nombre));
                });
            }, function (result) {
                console.log("La solicitud no se ha podido completar.");
            });
    });
});

