'use strict';

const MODEL = 'falta_itaca';

(function () {
    var currentId = null;

    function getHelpers() {
        return window.intranetUiHelpers || {};
    }

    function getModalElement(id) {
        return document.getElementById(id);
    }

    function showModal(id) {
        var helpers = getHelpers();
        if (typeof helpers.showModal === 'function') {
            helpers.showModal(id);
            return;
        }

        var modalElement = getModalElement(id);
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

    function hideModal(id) {
        var helpers = getHelpers();
        if (typeof helpers.hideModal === 'function') {
            helpers.hideModal(id);
            return;
        }

        var modalElement = getModalElement(id);
        if (!modalElement) {
            return;
        }

        if (window.bootstrap && window.bootstrap.Modal) {
            window.bootstrap.Modal.getOrCreateInstance(modalElement).hide();
            return;
        }

        if (window.jQuery) {
            window.jQuery(modalElement).modal('hide');
        }
    }

    function getProfileViewId(element) {
        var profileView = element ? element.closest('.profile_view') : null;
        return profileView ? profileView.id : null;
    }

    document.addEventListener('DOMContentLoaded', function () {
        var refuseButtons = document.querySelectorAll('.refuse');
        var convalidacionButtons = document.querySelectorAll('.convalidacion');
        var formDialogo = document.getElementById('formDialogo');
        var formPassword = document.getElementById('formPassword');
        var passwordSubmit = document.querySelector('#password .submit');
        var explicacion = document.getElementById('explicacion');

        for (var i = 0; i < refuseButtons.length; i += 1) {
            refuseButtons[i].addEventListener('click', function (event) {
                event.preventDefault();
                currentId = getProfileViewId(this);
                showModal('dialogo');
            });
        }

        if (formDialogo) {
            formDialogo.addEventListener('submit', function () {
                if (currentId) {
                    formDialogo.action = MODEL + '/' + currentId + '/refuse';
                }
            });
        }

        if (explicacion) {
            explicacion.focus();
        }

        for (var j = 0; j < convalidacionButtons.length; j += 1) {
            convalidacionButtons[j].addEventListener('click', function (event) {
                event.preventDefault();
                showModal('password');
            });
        }

        if (passwordSubmit) {
            passwordSubmit.addEventListener('click', function (event) {
                event.preventDefault();
                hideModal('password');

                if (formPassword) {
                    formPassword.submit();
                }

                showModal('loading');
            });
        }
    });
})();
