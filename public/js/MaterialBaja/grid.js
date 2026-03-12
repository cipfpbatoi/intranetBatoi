'use strict';

(function () {
    function configureDateOrdering() {
        var jq = window.$;
        if (!jq || !jq.fn || !jq.fn.dataTable || typeof jq.fn.dataTable.moment !== 'function') {
            return;
        }

        jq.fn.dataTable.moment('DD-MM-YYYY');
        jq.fn.dataTable.moment('DD-MM-YYYY HH:mm');
    }

    function getTableOptions() {
        return {
            language: {
                url: '/json/cattable.json'
            },
            dom: 'Bfrtip',
            deferRender: true,
            responsive: true,
            buttons: ['print'],
            columnDefs: [
                { responsivePriority: 1, targets: -1 }
            ]
        };
    }

    function initDataTable(tableElement, options) {
        if (!tableElement) {
            return;
        }

        if (typeof window.DataTable === 'function') {
            new window.DataTable(tableElement, options);
            return;
        }

        var jq = window.$;
        if (jq && jq.fn && typeof jq.fn.DataTable === 'function') {
            jq(tableElement).DataTable(options);
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        configureDateOrdering();
        initDataTable(document.getElementById('datatable'), getTableOptions());

        document.addEventListener('click', function (event) {
            var pdfLink = event.target.closest('a#pdf');
            if (!pdfLink) {
                return;
            }
            window.location.reload(true);
        });
    });
})();
