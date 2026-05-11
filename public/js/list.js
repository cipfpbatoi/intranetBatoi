(function () {
    function getDataTable() {
        if (!$.fn || !$.fn.dataTable) {
            return null;
        }

        if ($.fn.dataTable.isDataTable('#datatable')) {
            return $('#datatable').DataTable();
        }

        if (!$('#datatable').length) {
            return null;
        }

        return $('#datatable').DataTable({
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
    }
    function indexColumnaHorario(settings) {
        if (!settings || !settings.nTHead) {
            return -1;
        }

        var ths = settings.nTHead.querySelectorAll('th');
        for (var i = 0; i < ths.length; i++) {
            var text = (ths[i].textContent || '').trim().toLowerCase();
            if (text === 'horario' || text === 'horari') {
                return i;
            }
        }

        return -1;
    }

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

    var table = getDataTable();
    if (!table) {
        return;
    }

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

            var idxHorario = indexColumnaHorario(settings);
            if (idxHorario < 0) {
                return true;
            }

            var horario = data[idxHorario] || '';
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

    $(document)
        .off('change.turnoFiltro', '#turnoFiltro')
        .on('change.turnoFiltro', '#turnoFiltro', function () {
            var activeTable = getDataTable();
            if (activeTable) {
                activeTable.draw();
            }
        });

    $(document)
        .off('click.turnoFiltro', '#turnoFiltroReset')
        .on('click.turnoFiltro', '#turnoFiltroReset', function () {
            $('#turnoFiltro').val('todos');
            var activeTable = getDataTable();
            if (activeTable) {
                activeTable.draw();
            }
        });
})();
