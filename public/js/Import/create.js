'use strict';

(function () {
    function getHelpers() {
        return window.intranetUiHelpers || {};
    }

    function showModal(id) {
        var helpers = getHelpers();
        if (typeof helpers.showModal === 'function') {
            helpers.showModal(id);
            return;
        }

        var modalElement = document.getElementById(id);
        if (!modalElement) {
            return;
        }

        if (window.bootstrap && window.bootstrap.Modal) {
            window.bootstrap.Modal.getOrCreateInstance(modalElement).show();
            return;
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        var buttons = document.querySelectorAll('.submit');
        for (var i = 0; i < buttons.length; i += 1) {
            buttons[i].addEventListener('click', function () {
                showModal('loading');
            });
        }
    });
})();
