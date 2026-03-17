function initActividadDataTable() {
    if (typeof window.DataTable !== 'function') {
        return false;
    }

    var table = document.getElementById('datatable');
    if (!table) {
        return true;
    }

    if (table.dataset.dtInitialized === '1') {
        return true;
    }

    new window.DataTable(table, {
        language: {
            url: '/json/cattable.json',
        },
        deferRender: true,
        responsive: true,
        columnDefs: [
            { responsivePriority: 1, targets: -1 },
        ]
    });

    table.dataset.dtInitialized = '1';
    return true;
}

document.addEventListener('DOMContentLoaded', function () {
    var attempts = 0;
    var maxAttempts = 50; // ~5 segons
    var timer = setInterval(function () {
        attempts += 1;
        if (initActividadDataTable() || attempts >= maxAttempts) {
            clearInterval(timer);
        }
    }, 100);
});
