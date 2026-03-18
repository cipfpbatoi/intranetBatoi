(function () {
    'use strict';

    var MAX_RETRIES = 40;
    var RETRY_DELAY_MS = 250;

    function getTableHelper() {
        return window.intranetDataTable || {};
    }

    function hasDataTables() {
        var tableHelper = getTableHelper();
        var hasV2 = typeof tableHelper.hasDataTableV2 === 'function' && tableHelper.hasDataTableV2();
        var hasJqDt = typeof tableHelper.hasJQueryDataTable === 'function' && tableHelper.hasJQueryDataTable();

        return hasV2 || hasJqDt;
    }

    function initGrid() {
        var tableHelper = getTableHelper();
        var table = document.getElementById('datatable');

        if (!table) {
            return true;
        }

        if (!hasDataTables()) {
            return false;
        }

        if (typeof tableHelper.isInitialized === 'function' && tableHelper.isInitialized(table)) {
            return true;
        }

        // Evita salts visuals mentre DataTables calcula amplades.
        table.style.visibility = 'hidden';

        var dataTable = null;
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

        dataTable = typeof tableHelper.init === 'function'
            ? tableHelper.init(table, options)
            : null;

        if (!dataTable) {
            table.style.visibility = 'visible';
            return true;
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

        return true;
    }

    function bootGrid(retriesLeft) {
        if (initGrid()) {
            return;
        }

        if (retriesLeft <= 0) {
            console.warn('DataTables no està disponible: grid.js no s’inicialitza.');
            return;
        }

        window.setTimeout(function () {
            bootGrid(retriesLeft - 1);
        }, RETRY_DELAY_MS);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            bootGrid(MAX_RETRIES);
        });
        return;
    }

    bootGrid(MAX_RETRIES);
})();
