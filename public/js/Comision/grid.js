'use strict';

(function () {
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

    function initComisionGrid() {
        var tableHelper = getTableHelper();
        var table = document.getElementById('datatable');

        if (!table) {
            return true;
        }

        if (!hasDataTables()) {
            return false;
        }

        if (typeof tableHelper.registerMomentFormats === 'function') {
            tableHelper.registerMomentFormats(['DD-MM-YYYY', 'DD-MM-YYYY HH:mm']);
        }

        if (typeof tableHelper.isInitialized === 'function' && tableHelper.isInitialized(table)) {
            return true;
        }

        table.style.visibility = 'hidden';

        var dataTable = null;
        var options = {
            language: {
                url: '/json/cattable.json',
            },
            deferRender: true,
            responsive: {
                details: {
                    type: 'inline',
                    target: 'tr'
                }
            },
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

        return true;
    }

    function bootComisionGrid(retriesLeft) {
        if (initComisionGrid()) {
            return;
        }

        if (retriesLeft <= 0) {
            console.warn('DataTables no està disponible: Comision/grid.js no s’inicialitza.');
            return;
        }

        window.setTimeout(function () {
            bootComisionGrid(retriesLeft - 1);
        }, RETRY_DELAY_MS);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            bootComisionGrid(MAX_RETRIES);
        });
        return;
    }

    bootComisionGrid(MAX_RETRIES);
})();
