'use strict';
var href;
$(function () {

    $(".signatura").on("click", function (event) {
        event.preventDefault();
        $(this).attr("data-toggle", "modal").attr("data-target", "#signatura").attr("href", "");
        var token = $("#_token").text();
        var url = "/api/signatura/director";
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
    $(".sign").on("click", function (event) {
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
    $(".up").on("click", function (event) {
        event.preventDefault();
        href = $(this).parents("a").attr("href");
        $(this).attr("data-toggle", "modal").attr("data-target", "#upload").attr("href", "");
    });
    $("#formUpload").on("submit", function(){
        $(this).attr("action",href);
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