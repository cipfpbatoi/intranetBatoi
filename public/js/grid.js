(function ($) {
    if (!$.fn.dataTable) {
        console.warn('DataTables no està disponible: grid.js no s’inicialitza.');
        return;
    }

    if ($.fn.dataTable.moment) {
        $.fn.dataTable.moment('DD-MM-YYYY');
        $.fn.dataTable.moment('DD-MM-YYYY HH:mm');
    }

    const table = $('#datatable');
    const serverPagination = table.data('server-pagination') === true
        || table.data('server-pagination') === 'true'
        || table.is('[data-server-pagination]');

    table.DataTable({
        language: { url: '/json/cattable.json' },
        deferRender: true,
        responsive: true,
        paging: !serverPagination,
        info: !serverPagination,
        searching: !serverPagination, // evitarem incoherències amb la paginació de Laravel
        ordering: !serverPagination,
        columnDefs: [
            { responsivePriority: 1, targets: -1}
        ]
    });

    $(function(){
        $('a#pdf').on('click',function(){
            location.reload(true);
        });
    });
})(jQuery);
