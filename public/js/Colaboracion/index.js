'use strict';

$('#datatable').DataTable( {
    language: {
        url: '/json/cattable.json',
    },
    deferRender: true,
    responsive: true,
    columnDefs: [
        { responsivePriority: 1, targets: -1},
    ]
});