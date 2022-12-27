'use strict';

$('#datatable').DataTable( {
    language: {
        url: '/json/cattable.json',
    },
    deferRender: true,
    responsive: true,
    rowCallback: function (row,data){
        if (data[3].includes('Col·labora')) {
            $(row).addClass('bg-green')
        } else {
            if (data[3].includes('No col')) {
                $(row).addClass('bg-red')
            }

        }
    },
    columnDefs: [
        { responsivePriority: 1, targets: -1},
    ]
});