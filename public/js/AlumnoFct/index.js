'use strict';

$(function () {
    $(".download").on("click", function (event) {
        event.preventDefault();
        $(this).attr("data-toggle", "modal").attr("data-target", "#password").attr("href", "");
    });
    $(".signatura").on("click", function (event) {
        event.preventDefault();
        $(this).attr("data-toggle", "modal").attr("data-target", "#signatura").attr("href", "");
        var token = $("#_token").text();
        var url = "/api/signatura";
        $.ajax({
            method: "GET",
            url: url,
            dataType: 'json',
            data: {api_token: token}
        })
            .then(function (result) {
                pintaTablaSignatura(result.data);
            }, function (result) {
                console.log("La solicitud no se ha podido completar.");
            });
    });

    $("#seleccion .submit").click(function() {
        event.preventDefault();
        $("#checkall").prop('checked',false);
        $("#formSeleccion" ).submit();
    });


    $("#password .submit").click(function() {
        localStorage.setItem("cur_modal", '#password');
        event.preventDefault();
        $('#password').modal('hide');
        $("#formPassword" ).submit();
        $(this).attr("data-toggle", "modal").attr("data-target", "#loading").attr("href", "");
    });
});


function pintaTablaSignatura(newOptions){
    var $el = $("#tableSignatura");
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