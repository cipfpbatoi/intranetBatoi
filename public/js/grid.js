(function () {
    'use strict';

    if (typeof window.DataTable !== 'function') {
        console.warn('DataTables no està disponible: grid.js no s’inicialitza.');
        return;
    }

    var table = document.getElementById('datatable');
    if (!table) {
        return;
    }

    if (typeof window.DataTable.isDataTable === 'function' && window.DataTable.isDataTable(table)) {
        return;
    }

    // Evita salts visuals mentre DataTables calcula amplades.
    table.style.visibility = 'hidden';

    var dataTable = new window.DataTable(table, {
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
    });

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
