'use strict';

$('#datatable').DataTable( {
    language: {
        url: '/json/cattable.json',
    },
    dom: 'Bfrtip',
    buttons: [
        'print'
    ],
    deferRender: true,
    responsive: true,
    columnDefs: [
        { responsivePriority: 1, targets: -1},
    ]
});

