'use strict';

(function () {
    function showModal(id) {
        var modalElement = document.getElementById(id);
        if (!modalElement) {
            return;
        }

        if (window.bootstrap && window.bootstrap.Modal) {
            window.bootstrap.Modal.getOrCreateInstance(modalElement).show();
            return;
        }

        if (window.jQuery) {
            window.jQuery(modalElement).modal('show');
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
