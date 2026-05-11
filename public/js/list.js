(function () {
    function horaEnMinuts(hora) {
        var parts = (hora || '').split(':');
        if (parts.length < 2) {
            return null;
        }

        var h = parseInt(parts[0], 10);
        var m = parseInt(parts[1], 10);
        if (Number.isNaN(h) || Number.isNaN(m)) {
            return null;
        }

        return h * 60 + m;
    }

    function horaIniciHorario(text) {
        var match = (text || '').match(/(\d{2}:\d{2})\s*-/);
        return match ? match[1] : null;
    }

    var table = $('#datatable').DataTable({
        language: {
            url: '/json/cattable.json'
        },
        dom: 'Bfrtip',
        deferRender: true,
        responsive: true,
        buttons: [
            'print'
        ],
        columnDefs: [
            { responsivePriority: 1, targets: -1 }
        ]
    });

    if (!window.__turnoFiltroRegistrat) {
        window.__turnoFiltroRegistrat = true;
        $.fn.dataTable.ext.search.push(function (settings, data) {
            if (!settings || settings.nTable.getAttribute('id') !== 'datatable') {
                return true;
            }

            var filtro = ($('#turnoFiltro').val() || 'todos').toLowerCase();
            if (filtro === 'todos') {
                return true;
            }

            var horario = data[3] || '';
            var hora = horaIniciHorario(horario);
            var minuts = horaEnMinuts(hora);
            if (minuts === null) {
                return false;
            }

            var limitManana = 13 * 60 + 45;
            if (filtro === 'manana') {
                return minuts <= limitManana;
            }

            if (filtro === 'tarde') {
                return minuts > limitManana;
            }

            return true;
        });
    }

    $('#turnoFiltro').on('change', function () {
        table.draw();
    });

    $('#turnoFiltroReset').on('click', function () {
        $('#turnoFiltro').val('todos');
        table.draw();
    });
})();
