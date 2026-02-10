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

    table.DataTable({
        language: { url: '/json/cattable.json' },
        deferRender: true,
        responsive: true,
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
