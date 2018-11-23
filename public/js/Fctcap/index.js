'use strict';
    var token = $("#_token").text();
    var grupo = $("#_grupo").text();
    
    $("#dataFct").DataTable( {
        ajax : {
            method: "GET",
            url: '/api/alumnoFct/'+grupo+'/grupo',
            data: { api_token: token},
        },
        deferRender : true,
        dataSrc : 'data',
        columns: [
            { data:'id'},
            { data:'nombre'},
            { data:'centro'},
            { data:'desde'},
            { data:'hasta'},
            { data: null ,
              render: function (data ) {
                    if ( data.pg0301 ) 
                           return '<input type="checkbox" checked class="editor-active">';
                    else 
                            return '<input type="checkbox" class="editor-active">';
                },
            },
        ],
        language: {
            url: "/json/cattable.json"
        }
    });
    
$(function () {
        // checkBox inventario
        $('#dataFct').on('change', 'input.editor-active', function (event) {
            var idFct = $(this).parent().siblings().first().text();
            var pg0301 = $(this).prop("checked");
                $.ajax({
                    method: "PUT",
                    url: "/api/alumnoFct/"+idFct,
                    data: {
                        id: idFct, 
                        pg0301:  pg0301, 
                        api_token: token,
                    },
            }).then(function (res) {
                    console.log(res)
                }, function (res) {
                    if (pg0301) {$(this).removeProp("checked");}
                    else {$(this).prop("checked");}
                    console.log(res)
            });
        })  
    })

