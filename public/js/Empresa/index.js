'use strict';

const PRACTICAS=31;
const DUAL=37;


var autorizado=(!($('#rol').text()%PRACTICAS)||!($('#rol').text()%DUAL));



const COLUMNS=[
            {data: 'concierto'},
            {data: 'nombre'},
            {data: 'direccion'},
            {data: 'localidad'},
            {data: 'telefono'},
            {data: 'email'},
            {data: 'cif'},
            {data: 'actividad'},
            {data: null },
        ];
const ID = 'id';
const TABLA ='Empresa';
    
//$(function () {
    var token = $("#_token").text();
    $('#datatable').DataTable({
        language: {
            url: '/json/cattable.json'
        },
        ajax : {
            method: "GET",
            url: '/api/convenio',
            data: { api_token: token},
        },
        deferRender: true,
        dataSrc: 'data',
        columns: COLUMNS,
        rowId : ID,
        responsive: true,
        columnDefs: [
            {
                responsivePriority: 1,
                targets: COLUMNS.length-1,
                "render": function ( data,autorizado ) {
                        if (autorizado){
                            if (data.fichero)
                                return  `<a href="#" class="shown"><i class="fa fa-plus" title="Mostrar"></i></a> <a href="#" class="document"><i class="fa fa-eye" title="Anexe I"></i></a>`;
                            else
                                return  `<a href="#" class="shown"><i class="fa fa-plus" title="Mostrar"></i></a>`;
                        }
                }
            },
        ],
    });
    $('#datatable').on('click', 'a.delete', function (event) {
        let info="\n";
        let titles=$(this).parents('table').find('thead').find('th');
        $(this).parent().siblings().each(function(i, item) {
            if (item.innerHTML.trim().length>0) {
                info+=` - ${titles.eq(i).text().trim()}: ${item.innerHTML}\n`;
            }
        })
        if (confirm('Vas a borrar el elemento:'+info)) {
            $(this).attr("href","/"+TABLA.toLowerCase()+"/"+$(this).parent().parent().attr('id')+"/delete");
        } else {
            event.preventDefault();
        }
    })
    // Botón shown
    $('#datatable').on('click', 'a.shown', function (event) {
        $(this).attr("href","/"+TABLA.toLowerCase()+"/"+$(this).parent().parent().attr('id')+"/detalle");
    })
    $('#datatable').on('click', 'a.document', function (event) {
        $(this).attr("href","/"+TABLA.toLowerCase()+"/"+$(this).parent().parent().attr('id')+"/document");
    })
//});