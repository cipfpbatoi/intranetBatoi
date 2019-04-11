'use strict';
$(function() {
    var token = $("#_token").text();
    $(".resolve").on("click", function(event){
        event.preventDefault();
        var colaboracion = $(this).parents(".well");
        var boton = $(this);
        $.ajax({
            method: "GET",
            url: "/api/colaboracion/" + colaboracion.attr('id') + "/resolve",
            data: { api_token: token}
        }).then(function (result) {
            $("#tab_colabora").append(colaboracion.parent());
            boton.html('<i class="fa fa-frown-o"></i> ??');
            boton.attr('class',"btn-primary btn btn-xs iconButton");
            boton.on({click:function(){
                    event.preventDefault();
                    var colaboracion = $(this).parents(".well");
                    var boton = $(this);
                    $.ajax({
                        method: "GET",
                        url: "/api/colaboracion/" + colaboracion.attr('id') + "/unauthorize",
                        data: { api_token: token}
                    }).then(function (result) {
                        $("#tab_pendiente").append(colaboracion.parent());
                        boton.html('<i class="fa fa-smile-o"></i> SI');
                        boton.attr('class',"btn-success btn btn-xs iconButton");
                    });
                }});
        });
    });
    $(".refuse").on("click", function(){
        event.preventDefault();
        var colaboracion = $(this).parents(".well");
        var boton = $(this);
        $.ajax({
            method: "GET",
            url: "/api/colaboracion/" + colaboracion.attr('id') + "/refuse",
            data: { api_token: token}
        }).then(function (result) {
            $("#tab_descartada").append(colaboracion.parent());
            boton.hide();
        });
    });
    $(".unauthorize").on("click", function(){
        event.preventDefault();
        var colaboracion = $(this).parents(".well");
        var boton = $(this);
        $.ajax({
            method: "GET",
            url: "/api/colaboracion/" + colaboracion.attr('id') + "/unauthorize",
            data: { api_token: token}
        }).then(function (result) {
            $("#tab_pendiente").append(colaboracion.parent());
            boton.html('<i class="fa fa-smile-o"></i> SI');
            boton.attr('class',"btn-success resolve btn btn-xs iconButton");
        });
    });
})