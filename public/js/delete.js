'use strict';

(function () {
    function getHelpers() {
        return window.intranetUiHelpers || {};
    }

    function buildRowInfo(source) {
        var helpers = getHelpers();
        if (typeof helpers.buildRowInfo === 'function') {
            return helpers.buildRowInfo(source);
        }

        var row = source ? source.closest('tr') : null;
        var table = source ? source.closest('table') : null;
        if (!row || !table) {
            return '\n';
        }

        var headers = table.querySelectorAll('thead th');
        var cells = row.querySelectorAll('td');
        var info = '\n';

        for (var i = 0; i < cells.length; i += 1) {
            var cell = cells[i];
            if (!cell || cell.contains(source)) {
                continue;
            }

            var text = (cell.textContent || '').trim();
            if (!text) {
                continue;
            }

            var header = headers[i] ? (headers[i].textContent || '').trim() : '';
            var value = cell.firstElementChild ? cell.firstElementChild.innerHTML : text;
            info += ' - ' + header + ': ' + value + '\n';
        }

        return info;
    }

    function confirmAction(prefix, info) {
        var helpers = getHelpers();
        if (typeof helpers.confirmAction === 'function') {
            return helpers.confirmAction(prefix, info);
        }
        return window.confirm(prefix + info);
    }

    function bindSimpleConfirmButtons() {
        var buttons = document.querySelectorAll('.confirm');
        for (var i = 0; i < buttons.length; i += 1) {
            buttons[i].addEventListener('click', function (event) {
                var info = (this.textContent || '').trim();
                if (!confirmAction('Confirma que vols realitzar la següent operació: ', info)) {
                    event.preventDefault();
                }
            });
        }
    }

    function bindDatatableActions() {
        var datatable = document.getElementById('datatable');
        if (!datatable) {
            return;
        }

        datatable.addEventListener('click', function (event) {
            var erase = event.target.closest('.fa-eraser');
            var envelope = event.target.closest('.fa-envelope');

            if (!erase && !envelope) {
                return;
            }

            var info = buildRowInfo(erase || envelope);
            var message = erase
                ? "Vas a esborrar l'element:"
                : "Vas a tramitar l'element:";

            if (!confirmAction(message, info)) {
                event.preventDefault();
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        bindSimpleConfirmButtons();
        bindDatatableActions();
    });
})();
