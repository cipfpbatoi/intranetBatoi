'use strict';

(function (global) {
    function getModalElement(id) {
        return document.getElementById(id);
    }

    function showModal(id) {
        var modalElement = getModalElement(id);
        if (!modalElement) {
            return;
        }

        if (global.bootstrap && global.bootstrap.Modal) {
            global.bootstrap.Modal.getOrCreateInstance(modalElement).show();
            return;
        }

        if (global.jQuery) {
            global.jQuery(modalElement).modal('show');
        }
    }

    function hideModal(id) {
        var modalElement = getModalElement(id);
        if (!modalElement) {
            return;
        }

        if (global.bootstrap && global.bootstrap.Modal) {
            global.bootstrap.Modal.getOrCreateInstance(modalElement).hide();
            return;
        }

        if (global.jQuery) {
            global.jQuery(modalElement).modal('hide');
        }
    }

    function buildRowInfo(sourceElement) {
        var row = sourceElement ? sourceElement.closest('tr') : null;
        var table = sourceElement ? sourceElement.closest('table') : null;
        if (!row || !table) {
            return '\n';
        }

        var headers = table.querySelectorAll('thead th');
        var cells = row.querySelectorAll('td');
        var info = '\n';

        for (var i = 0; i < cells.length; i += 1) {
            var cell = cells[i];
            if (!cell || cell.contains(sourceElement)) {
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

    function confirmAction(prefix, details) {
        return global.confirm(prefix + details);
    }

    global.intranetUiHelpers = {
        showModal: showModal,
        hideModal: hideModal,
        buildRowInfo: buildRowInfo,
        confirmAction: confirmAction
    };
})(window);
