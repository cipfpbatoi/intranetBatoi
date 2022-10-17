$(".selecciona").on("click",function(event){
    event.preventDefault();
    $(this).attr("data-toggle", "modal").attr("data-target", "#seleccion").attr("href", "");
    var token = $("#_token").text();
    var url = $(this).attr("data-url");
    $('#formSeleccion').attr("action",url.substring(4));
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

function pintaTablaSeleccion(newOptions){
    var $el = $("#tableSeleccion");
    var checked;
    var emptys = 0;
    var checks = 0;
    $el.empty(); // remove old options
    $.each(newOptions, function (key, value) {
        if (value.marked  || value.marked == null){
            checked = ' checked';
            checks += 1;
        }
        else {
            checked = '';
            emptys += 1;
        }
        $el.append($("<tr><td><input type='checkbox' class='elements' name='"+value.id+"'"+checked+"> "+value.texto+"</td></tr>"));
    });
    if (checks > emptys) {
        checked = 'checked';
    } else {
        checked = ' ';
    }
    $el.append($("<tr><td>-------------------------------</td></tr>"))
    $el.append($("<tr><td><div id='divCheckAll'><input type='checkbox' name='checkall' id='checkall' onClick='check_uncheck_checkbox(this.checked);'"+checked+"/>Check All</div></td></tr>"));
}

function check_uncheck_checkbox(isChecked) {
    if(isChecked) {
        $('input.elements').each(function() {
            this.checked = true;
        });
    } else {
        $('input.elements').each(function() {
            this.checked = false;
        });
    }
}
