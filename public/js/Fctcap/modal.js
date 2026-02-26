'use strict';
var profesor;
function apiAuthOptions(extraData) {
    var legacyToken = $.trim($("#_token").text());
    var bearerToken = $.trim($('meta[name="user-bearer-token"]').attr('content') || "");
    var data = extraData || {};
    var headers = {};

    if (bearerToken) {
        headers.Authorization = "Bearer " + bearerToken;
    } else if (legacyToken) {
        data.api_token = legacyToken;
    }

    return { headers: headers, data: data };
}

$(function () {
    // checkBox inventario
    $('#dataFct').on('change', 'input.editor-active', function (event) {
        var idFct = $(this).parent().siblings().first().text();
        var pg0301 = $(this).prop("checked");
        var a56 = document.activeElement.classList.contains('a56');
        if (a56){
            var authA56 = apiAuthOptions({
                id: idFct,
                a56: pg0301,
            });
            $.ajax({
                method: "PUT",
                url: "/api/alumnoFct/"+idFct,
                headers: authA56.headers,
                data: authA56.data,
            }).then(function (res) {
                console.log(res)
            }, function (res) {
                if (pg0301) {$(this).removeProp("checked");}
                else {$(this).prop("checked");}
                console.log(res)
            });
        } else {
            var authPg = apiAuthOptions({
                id: idFct,
                pg0301: pg0301,
            });
            $.ajax({
                method: "PUT",
                url: "/api/alumnoFct/"+idFct,
                headers: authPg.headers,
                data: authPg.data,
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
        var authGet = apiAuthOptions();
        $.ajax({
            method: "GET",
            url: "/api/alumnoFct/"+id,
            headers: authGet.headers,
            data: authGet.data,
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
