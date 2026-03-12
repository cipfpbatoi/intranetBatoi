(function () {
    'use strict';

    var hasV2 = typeof window.DataTable === 'function';
    var hasJqDt = !!(window.jQuery && window.jQuery.fn && typeof window.jQuery.fn.DataTable === 'function');

    if (!hasV2 && !hasJqDt) {
        console.warn('DataTables no està disponible: grid.js no s’inicialitza.');
        return;
    }

    var table = document.getElementById('datatable');
    if (!table) {
        return;
    }

    if (hasV2 && typeof window.DataTable.isDataTable === 'function' && window.DataTable.isDataTable(table)) {
        return;
    }
    if (hasJqDt && window.jQuery.fn.dataTable && typeof window.jQuery.fn.dataTable.isDataTable === 'function' && window.jQuery.fn.dataTable.isDataTable(table)) {
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

    var dataTable = hasV2
        ? new window.DataTable(table, options)
        : window.jQuery(table).DataTable(options);

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
