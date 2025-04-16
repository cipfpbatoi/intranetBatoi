$.fn.dataTable.moment( 'DD-MM-YYYY' );
$.fn.dataTable.moment( 'DD-MM-YYYY HH:mm' );
$('#datatable').DataTable( {
    language: {
        url: '/json/cattable.json'
    },
    dom: 'Bfrtip',
    deferRender: true,
    responsive: true,
    buttons: [
        'print'
    ],
    columnDefs: [
        { responsivePriority: 1, targets: -1},
    ]
});

$(function(){
    $('a#pdf').on('click',function(){
        location.reload(true);
    });

})