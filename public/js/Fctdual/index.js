'use strict';

function apiAuthOptions(extraData) {
    var legacyToken = $.trim($("#_token").text());
    var bearerToken = $.trim($('meta[name="user-bearer-token"]').attr('content') || "");
    var data = extraData || {};
    var headers = {};

    if (bearerToken) {
        headers.Authorization = "Bearer " + bearerToken;
    }
    if (legacyToken) {
        data.api_token = legacyToken;
    }

    return { headers: headers, data: data };
}

var grupo = $("#_grupo").text();
var avise = ' <a href="/profesor/mensaje" class="mensaje"><i class="fa fa-bell"></i></a> ';

var authDatatable = apiAuthOptions();
$("#dataFct").DataTable( {
    ajax : {
        method: "GET",
        url: '/api/alumnofct/'+grupo+'/dual',
        headers: authDatatable.headers,
        data: authDatatable.data,
    },
    deferRender : true,
    dataSrc : 'data',
    columns: [
        { data:'id'},
        { data:'nombre'},
        { data:'centro'},
        { data:'desde'},
        { data:'hasta'},
        { data: null,
            render: function (data){
                var ret =' <a href="/fct/'+data.id+'/link" class="imgButton"><i class="fa fa-paperclip"></i></a> ' + avise;
                if ( data.a56 === 1 ){
                    ret +=' <a href="/fct/'+data.id+'/sendAnexo" class="imgButton"><i class="fa fa-plane"></i></a> ';
                }
                return ret;
            }
        },
        { data: null ,
          render: function (data ) {
              if ( data.pg0301 ) {
                    return '<input type="checkbox" checked class="editor-active">';
                }
                else {
                    return '<input type="checkbox" class="editor-active"> ';
                }
            }
        },
        { data: null ,
            render: function (data ) {
                if ( data.a56 ) {
                    if (data.pg0301) {
                        return ' <input type="checkbox" checked class="editor-active a56">';
                    } else{
                        return ' <input type="checkbox" disabled checked class="editor-active a56">';
                    }
                }
                else {
                    if (data.pg0301) {
                        return ' <input type="checkbox" class="editor-active a56">';
                    } else{
                        return ' <input type="checkbox" disabled class="editor-active a56">';
                    }
                }
            }
        },
    ],
    language: {
        url: "/json/cattable.json"
    }
});

$(function () {
    // checkBox inventario
    $('#dataFct').on('change', 'input.editor-active', function (event) {
        var self = document.activeElement;
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
                url: "/api/alumnofct/"+idFct,
                headers: authA56.headers,
                data: authA56.data,
            }).then(function (res) {
                console.log(res)
            }, function (res) {
                if (pg0301) {
                    $(this).removeProp("checked");
                }
                else {
                    $(this).prop("checked");
                }
                console.log(res)
            });
        } else {
            var authPg = apiAuthOptions({
                id: idFct,
                pg0301: pg0301,
            });
            $.ajax({
                method: "PUT",
                url: "/api/alumnofct/"+idFct,
                headers: authPg.headers,
                data: authPg.data,
            }).then(function (res) {
                console.log(res)
            }, function (res) {
                if (pg0301) {$(this).removeProp("checked");}
                else {$(this).prop("checked");}
                console.log(res)
            });
        }
    });
});
