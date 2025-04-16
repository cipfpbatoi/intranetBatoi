'use strict';
var profesor;
$(function () {
    // checkBox inventario
    $('#dataFct').on('change', 'input.editor-active', function (event) {
        var idFct = $(this).parent().siblings().first().text();
        var pg0301 = $(this).prop("checked");
        var a56 = document.activeElement.classList.contains('a56');
        if (a56){
            $.ajax({
                method: "PUT",
                url: "/api/alumnoFct/"+idFct,
                data: {
                    id: idFct,
                    a56:  pg0301,
                    api_token: token,
                },
            }).then(function (res) {
                console.log(res)
            }, function (res) {
                if (pg0301) {$(this).removeProp("checked");}
                else {$(this).prop("checked");}
                console.log(res)
            });
        } else {
            $.ajax({
                method: "PUT",
                url: "/api/alumnoFct/"+idFct,
                data: {
                    id: idFct,
                    pg0301:  pg0301,
                    api_token: token,
                },
            }).then(function (res) {
                if (!pg0301){
                    document.activeElement.parentElement.nextElementSibling.children.item(0).disabled = true;
                } else {
                    document.activeElement.parentElement.nextElementSibling.children.item(0).disabled = false;
                }
            }, function (res) {
                if (pg0301) {$(this).removeProp("checked");}
                else {$(this).prop("checked");}
                console.log(res)
            });
        }
    });
    $("#dataFct").on("click",".mensaje" ,function(event){
        event.preventDefault();
        $(this).attr("data-toggle","modal").attr("data-target", "#aviso").attr("href","");
        let id=$(this).parent().siblings().first().text();
        $.ajax({
            method: "GET",
            url: "/api/alumnoFct/"+id,
            data: {
                api_token: token,
            },
        }).then(function (res) {
            $("#explicacion").text('Revisió Documentació Fct Alumne '+res.data.alumno+' :');
            profesor = res.data.profesor;
            console.log(res);
        },function (res){
            console.log(res);
        });
    });
    $("#formAviso").on("submit", function(){
        $(this).attr("action","/profesor/"+profesor+"/mensaje");
    });
});
