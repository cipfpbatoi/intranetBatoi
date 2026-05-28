    $('#datatable').DataTable({
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
