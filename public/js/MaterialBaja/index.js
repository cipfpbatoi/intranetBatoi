'use strict';

(function () {
    document.addEventListener('DOMContentLoaded', function () {
        var datatable = document.getElementById('datatable');
        if (!datatable) {
            return;
        }

        datatable.addEventListener('click', function (event) {
            var button = event.target.closest('.fa-remove');
            if (!button) {
                return;
            }

            var row = button.closest('tr');
            var table = button.closest('table');
            if (!row || !table) {
                return;
            }

            var titles = table.querySelectorAll('thead th');
            var cells = row.querySelectorAll('td');
            var info = '\n';

            for (var i = 0; i < cells.length; i += 1) {
                var cell = cells[i];
                if (!cell || cell.contains(button)) {
                    continue;
                }

                var text = (cell.textContent || '').trim();
                if (!text) {
                    continue;
                }

                var title = titles[i] ? (titles[i].textContent || '').trim() : '';
                var value = cell.firstElementChild ? cell.firstElementChild.innerHTML : text;
                info += ' - ' + title + ': ' + value + '\n';
            }

            if (!window.confirm("Vas a rebutjar la baixa de l'element:" + info)) {
                event.preventDefault();
            }
        });
    });
})();
