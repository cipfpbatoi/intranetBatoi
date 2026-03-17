'use strict';

(function () {
    var table = document.getElementById('datatable');
    var tableHelper = window.intranetDataTable || {};
    var hasV2 = typeof tableHelper.hasDataTableV2 === 'function' && tableHelper.hasDataTableV2();
    var hasJqDt = typeof tableHelper.hasJQueryDataTable === 'function' && tableHelper.hasJQueryDataTable();

    if (!table || (!hasV2 && !hasJqDt)) {
        return;
    }

    if (typeof tableHelper.registerMomentFormats === 'function') {
        tableHelper.registerMomentFormats(['DD-MM-YYYY', 'DD-MM-YYYY HH:mm']);
    }

    if (typeof tableHelper.isInitialized === 'function' && tableHelper.isInitialized(table)) {
        return;
    }

    table.style.visibility = 'hidden';

    var dataTable;
    var options = {
        language: {
            url: '/json/cattable.json',
        },
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

    dataTable = typeof tableHelper.init === 'function'
        ? tableHelper.init(table, options)
        : null;

    if (!dataTable) {
        table.style.visibility = 'visible';
        return;
    }

    table.addEventListener('draw.dt', function () {
        if (dataTable && dataTable.columns && typeof dataTable.columns.adjust === 'function') {
            dataTable.columns.adjust();
        }
    });

    table.addEventListener('responsive-resize.dt', function () {
        if (dataTable && dataTable.columns && typeof dataTable.columns.adjust === 'function') {
            dataTable.columns.adjust();
        }
    });

    window.addEventListener('resize', function () {
        if (dataTable && dataTable.columns && typeof dataTable.columns.adjust === 'function') {
            dataTable.columns.adjust();
        }
    });
})();
