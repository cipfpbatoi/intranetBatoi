(function () {
    'use strict';

    var tableHelper = window.intranetDataTable || {};
    var hasV2 = typeof tableHelper.hasDataTableV2 === 'function' && tableHelper.hasDataTableV2();
    var hasJqDt = typeof tableHelper.hasJQueryDataTable === 'function' && tableHelper.hasJQueryDataTable();

    if (!hasV2 && !hasJqDt) {
        console.warn('DataTables no està disponible: grid.js no s’inicialitza.');
        return;
    }

    var table = document.getElementById('datatable');
    if (!table) {
        return;
    }

    if (typeof tableHelper.isInitialized === 'function' && tableHelper.isInitialized(table)) {
        return;
    }

    // Evita salts visuals mentre DataTables calcula amplades.
    table.style.visibility = 'hidden';

    var options = {
        language: { url: '/json/cattable.json' },
        deferRender: true,
        responsive: true,
        autoWidth: false,
        columnDefs: [
            { responsivePriority: 1, targets: -1 }
        ],
        initComplete: function () {
            if (dataTable && dataTable.columns && typeof dataTable.columns.adjust === 'function') {
                dataTable.columns.adjust();
            }
            table.style.visibility = 'visible';
        }
    };

    var dataTable = typeof tableHelper.init === 'function'
        ? tableHelper.init(table, options)
        : null;

    if (!dataTable) {
        table.style.visibility = 'visible';
        return;
    }

    table.addEventListener('draw.dt', function () {
        dataTable.columns.adjust();
    });

    table.addEventListener('responsive-resize.dt', function () {
        dataTable.columns.adjust();
    });

    window.addEventListener('resize', function () {
        dataTable.columns.adjust();
    });

    document.querySelectorAll('a#pdf').forEach(function (link) {
        link.addEventListener('click', function () {
            location.reload(true);
        });
    });
})();
