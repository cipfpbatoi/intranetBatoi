var $ = window.jQuery || window.$;

function initActividadDataTable() {
    if (!$.fn || !$.fn.DataTable || !$.fn.dataTable) {
        return false;
    }

    if ($.fn.dataTable.moment && typeof $.fn.dataTable.moment === 'function') {
        $.fn.dataTable.moment('DD-MM-YYYY');
        $.fn.dataTable.moment('DD-MM-YYYY HH:mm');
    }

    var $table = $('#datatable');
    if (!$table.length) {
        return true;
    }

    if ($.fn.dataTable.isDataTable && $.fn.dataTable.isDataTable($table[0])) {
        return true;
    }

    $table.DataTable({
        language: {
            url: '/json/cattable.json',
        },
        deferRender: true,
        responsive: true,
        columnDefs: [
            { responsivePriority: 1, targets: -1 },
        ]
    });

    return true;
}

$(function () {
    var attempts = 0;
    var maxAttempts = 50; // ~5 segons
    var timer = setInterval(function () {
        attempts += 1;
        if (initActividadDataTable() || attempts >= maxAttempts) {
            clearInterval(timer);
        }
    }, 100);
});
