'use strict';

(function () {
    var uploadHref = '';

    function getHelpers() {
        return window.intranetUiHelpers || {};
    }

    function getApiAuth() {
        return window.intranetApiAuth || {};
    }

    function apiGet(url) {
        var apiAuth = getApiAuth();
        if (typeof apiAuth.apiGet === 'function') {
            return apiAuth.apiGet(url);
        }

        return Promise.reject(new Error('intranetApiAuth.apiGet no disponible'));
    }

    function setModalAttrs(element, targetId) {
        if (!element) {
            return;
        }

        element.setAttribute('href', '');
    }

    function openModal(id) {
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

    function hideModal(id) {
        var helpers = getHelpers();
        if (typeof helpers.hideModal === 'function') {
            helpers.hideModal(id);
            return;
        }

        var modalElement = document.getElementById(id);
        if (!modalElement) {
            return;
        }

        if (window.bootstrap && window.bootstrap.Modal) {
            window.bootstrap.Modal.getOrCreateInstance(modalElement).hide();
            return;
        }
    }

    function setCheckedAndDisabled(id, checked, disabled) {
        var element = document.getElementById(id);
        if (!element) {
            return;
        }

        element.checked = checked;
        element.disabled = disabled;
    }

    function bindApiButton(selector, url, beforeRequest) {
        document.querySelectorAll(selector).forEach(function (button) {
            button.addEventListener('click', function (event) {
                event.preventDefault();
                setModalAttrs(button, 'signatura');
                openModal('signatura');

                if (typeof beforeRequest === 'function') {
                    beforeRequest();
                }

                apiGet(url)
                    .then(function (result) {
                        if (typeof window.pintaTablaSeleccion === 'function') {
                            window.pintaTablaSeleccion(result.data, '#tableSignatura');
                        }
                    })
                    .catch(function () {
                        console.log('La solicitud no se ha podido completar.');
                    });
            });
        });
    }

    function initUploadButton() {
        document.querySelectorAll('.up').forEach(function (button) {
            button.addEventListener('click', function (event) {
                event.preventDefault();
                var anchor = button.closest('a');
                uploadHref = anchor ? (anchor.getAttribute('href') || '') : '';
                setModalAttrs(button, 'upload');
                openModal('upload');
            });
        });

        var formUpload = document.getElementById('formUpload');
        if (formUpload) {
            formUpload.addEventListener('submit', function () {
                formUpload.setAttribute('action', uploadHref);
            });
        }
    }

    function initSubmitButtons() {
        document.querySelectorAll('#signatura .submit, #signaturaA1 .submit').forEach(function (button) {
            button.addEventListener('click', function () {
                hideModal('signatura');
                setModalAttrs(button, 'loading');
                openModal('loading');
            });
        });
    }

    function initCheckboxBehaviours() {
        var fileInput = document.getElementById('file');
        var aa3 = document.getElementById('AA3');
        if (fileInput && aa3) {
            fileInput.addEventListener('change', function () {
                aa3.disabled = !fileInput.value;
            });
        }

        var a1 = document.getElementById('A1');
        var a5 = document.getElementById('A5');

        if (a1 && a5) {
            a1.addEventListener('change', function () {
                if (a1.checked) {
                    a5.checked = false;
                }
            });

            a5.addEventListener('change', function () {
                if (a5.checked) {
                    a1.checked = false;
                }
            });
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        bindApiButton('.signatura', '/api/signatura/director');

        bindApiButton('.sign', '/api/signatura', function () {
            setCheckedAndDisabled('A5', false, true);
        });

        bindApiButton('.a1', '/api/signatura/a1', function () {
            setCheckedAndDisabled('A2', false, true);
            setCheckedAndDisabled('A3', false, true);
            setCheckedAndDisabled('AA3', false, true);
        });

        initUploadButton();
        initSubmitButtons();
        initCheckboxBehaviours();
    });
})();
