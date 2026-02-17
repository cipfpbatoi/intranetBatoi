(function ($) {
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
    
if ($.fn.dataTable && $.fn.dataTable.isDataTable('#datatable')) {
    // Ja inicialitzada per una altra capa.
} else {
    const table = $('#datatable');
    if (!table.length) {
        return;
    }

    // Evita salts visuals mentre es calculen les amplades.
    table.css('visibility', 'hidden');

    var token = $("#_token").text();
    const dataTable = table.DataTable({
        language: {
            url: '/json/cattable.json'
        },
        ajax : {
            method: "GET",
            url: '/api/convenio',
            data: { api_token: token},
        },
        deferRender: true,
        autoWidth: false,
        columns: COLUMNS,
        rowId : ID,
        responsive: true,
        rowCallback: function (row, data){
            if (data.conveni) {
                $(row).addClass('bg-green')
            }
        },
        columnDefs: [
            {
                responsivePriority: 1,
                targets: COLUMNS.length-1,
                "render": function () {
                        if (autorizado){
                            return  `<a href="#" class="shown"><i class="fa fa-plus" title="Mostrar"></i></a>`;
                        }
                }
            },
        ],
        initComplete: function () {
            const api = this.api();
            api.columns.adjust();
            $(api.table().node()).css('visibility', 'visible');
        }
    });
    table.on('draw.dt responsive-resize.dt', function () {
        dataTable.columns.adjust();
    });

    $(window).on('resize', function () {
        dataTable.columns.adjust();
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
}
})(jQuery);
