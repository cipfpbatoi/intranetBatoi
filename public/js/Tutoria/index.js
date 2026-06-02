'use strict';
$(document).ready(function () {
    var $table = $('#datatable');
    if (!$table.length) { return; }

    function applyOrder() {
        $table.DataTable({ retrieve: true }).order([2, 'desc']).draw();
    }

    if ($.fn.dataTable && $.fn.dataTable.isDataTable($table[0])) {
        applyOrder();
    } else {
        $table.on('init.dt', applyOrder);
    }
});
