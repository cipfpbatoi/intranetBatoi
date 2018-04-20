$('#datatable').DataTable( {
    language: {
        url: '/json/cattable.json',
    },
    deferRender: true,
    responsive: true,
    paging : false,
    
    columnDefs: [
        { responsivePriority: 1, targets: -1},
    ]
});

