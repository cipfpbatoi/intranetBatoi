'use strict';

const MODEL = 'expediente';

(function () {
    var currentId = null;

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

        if (window.jQuery) {
            window.jQuery(modalElement).modal('show');
        }
    }

    function getProfileViewId(element) {
        var profileView = element ? element.closest('.profile_view') : null;
        return profileView ? profileView.id : null;
    }

    document.addEventListener('DOMContentLoaded', function () {
        var refuseButtons = document.querySelectorAll('.refuse');
        var userButtons = document.querySelectorAll('.user');
        var formDialogo = document.getElementById('formDialogo');
        var formSelect = document.getElementById('formSelect');
        var dialogo = document.getElementById('dialogo');

        for (var i = 0; i < refuseButtons.length; i += 1) {
            refuseButtons[i].addEventListener('click', function (event) {
                event.preventDefault();
                currentId = getProfileViewId(this);
                showModal('dialogo');
            });
        }

        for (var j = 0; j < userButtons.length; j += 1) {
            userButtons[j].addEventListener('click', function (event) {
                event.preventDefault();
                currentId = getProfileViewId(this);
                showModal('select');
            });
        }

        if (formDialogo) {
            formDialogo.addEventListener('submit', function () {
                if (currentId) {
                    formDialogo.action = MODEL + '/' + currentId + '/refuse';
                }
            });
        }

        if (formSelect) {
            formSelect.addEventListener('submit', function () {
                if (currentId) {
                    formSelect.action = MODEL + '/' + currentId + '/assigna';
                }
            });
        }

        if (dialogo) {
            dialogo.focus();
        }
    });
})();
