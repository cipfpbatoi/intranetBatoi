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
    if (!table.length) {
        return;
    }
    if ($.fn.dataTable.isDataTable(table)) {
        return;
    }

    // Evita salts visuals mentre DataTables calcula amplades.
    table.css('visibility', 'hidden');

    const dataTable = table.DataTable({
        language: { url: '/json/cattable.json' },
        deferRender: true,
        responsive: true,
        autoWidth: false,
        columnDefs: [
            { responsivePriority: 1, targets: -1}
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

    $(function(){
        $('a#pdf').on('click',function(){
            location.reload(true);
        });
    });
})(jQuery);
