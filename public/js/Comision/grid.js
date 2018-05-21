$.fn.dataTable.moment( 'DD-MM-YYYY' );
$.fn.dataTable.moment( 'DD-MM-YYYY HH:mm' );
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


