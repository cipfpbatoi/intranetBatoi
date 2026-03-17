'use strict';

(function () {
    function getHelpers() {
        return window.intranetUiHelpers || {};
    }

    function getApiAuth() {
        return window.intranetApiAuth || {};
    }

    function trim(value) {
        return (value || '').toString().trim();
    }

    function getModelo() {
        var datatable = document.getElementById('datatable');
        var modelName = datatable ? datatable.getAttribute('name') : '';
        return trim(modelName).toLowerCase();
    }

    function getFormModal() {
        return document.querySelector('.modal form');
    }

    function getCurrentUrlWithoutHash() {
        return window.location.href.replace(/#/, '');
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

    function setInputValue(field, value) {
        if (!field) {
            return;
        }

        var tagName = field.tagName.toUpperCase();
        var type = (field.getAttribute('type') || '').toUpperCase();

        if (tagName === 'INPUT' && type === 'CHECKBOX') {
            field.checked = !!value;
            return;
        }

        if (tagName === 'INPUT' && type === 'FILE') {
            var fileLabel = document.querySelector("[id='Fichero Actual']");
            if (fileLabel) {
                fileLabel.textContent = value;
            }
            return;
        }

        field.value = value;
    }

    function fillEditForm(data, id) {
        var formModal = getFormModal();
        if (!formModal) {
            return;
        }

        formModal.setAttribute('action', getCurrentUrlWithoutHash() + '/' + id + '/edit');

        var metodo = formModal.querySelector('#metodo');
        if (metodo) {
            metodo.value = 'PUT';
        }

        var hiddenId = formModal.querySelector('#id');
        if (hiddenId) {
            hiddenId.value = id;
        }

        var primerElem = '';
        Object.keys(data || {}).forEach(function (propiedad) {
            var elem = document.getElementById(propiedad + '_id');
            if (!elem) {
                return;
            }

            if (!primerElem) {
                primerElem = propiedad;
            }

            setInputValue(elem, data[propiedad]);

            if (data[propiedad] !== '' && typeof elem.focus === 'function') {
                elem.focus();
            }
        });

        if (typeof window.postModal === 'function') {
            window.postModal();
        }

        if (primerElem) {
            var firstField = document.getElementById(primerElem + '_id');
            if (firstField && typeof firstField.focus === 'function') {
                firstField.focus();
            }
        }
    }

    function renderShowData(data) {
        var campos = document.getElementById('campos');
        if (!campos) {
            return;
        }

        var html = '<ul class="to_do">';
        Object.keys(data || {}).forEach(function (propiedad) {
            if (propiedad === 'fichero' && data[propiedad] !== null) {
                html += "<li><img src='storage/" + data[propiedad] + "' height='400' width='300'/>'</li>";
            } else {
                html += "<li><strong style='text-transform: capitalize'>" + propiedad + '</strong>: ' + data[propiedad] + '</li>';
            }
        });
        html += '</ul>';

        campos.innerHTML = html;
    }

    function initCreateButton() {
        document.querySelectorAll('a.btn-primary.btn.txtButton').forEach(function (button) {
            button.addEventListener('click', function (event) {
                event.preventDefault();

                var formModal = getFormModal();
                var idField = document.getElementById('id');
                if (idField) {
                    idField.value = '';
                }

                var horizontalForm = document.querySelector('.form-horizontal');
                if (horizontalForm && typeof horizontalForm.reset === 'function') {
                    horizontalForm.reset();
                }

                if (formModal) {
                    formModal.setAttribute('action', getCurrentUrlWithoutHash());
                }

                var metodoField = document.getElementById('metodo');
                if (metodoField) {
                    metodoField.value = 'POST';
                }

                setModalAttrs(button, 'create');
                showModal('create');
            });
        });
    }

    function initDangerAlertModalRestore() {
        var hasDanger = document.querySelectorAll('div .alert-danger').length > 0;
        if (!hasDanger) {
            return;
        }

        var idField = document.getElementById('id');
        var idValue = idField ? idField.value : '';

        if (Number(idValue) > 0) {
            var formModal = getFormModal();
            if (formModal) {
                var action = formModal.getAttribute('action') || '';
                formModal.setAttribute('action', action + '/' + idValue + '/edit');
            }
        }

        var curModal = localStorage.getItem('cur_modal');
        if (!curModal) {
            curModal = '#create';
        } else {
            localStorage.removeItem('cur_modal');
        }

        var modalId = curModal.replace(/^#/, '');
        showModal(modalId);
    }

    function initCreateHiddenHandler() {
        var createModal = document.getElementById('create');
        if (!createModal) {
            return;
        }

        createModal.addEventListener('hidden.bs.modal', function () {
            var idField = createModal.querySelector('#id');
            var idValue = idField ? trim(idField.value) : '';
            if (!idValue) {
                return;
            }

            var row = document.getElementById(idValue);
            if (!row) {
                return;
            }

            var editIcon = row.querySelector('.fa-edit');
            if (!editIcon) {
                return;
            }

            var anchor = editIcon.closest('a');
            if (!anchor) {
                return;
            }

            anchor.setAttribute('href', getCurrentUrlWithoutHash() + '/' + idValue + '/edit');
        });
    }

    function initAuxClickGuard() {
        document.addEventListener('auxclick', function (event) {
            var target = event.target;
            if (!(target instanceof Element) || !target.matches('.fa-edit')) {
                return;
            }

            if (event.which === 2) {
                event.preventDefault();
            }
        });
    }

    function initEditTemporaryDisable() {
        document.querySelectorAll('.fa-edit').forEach(function (elem) {
            elem.classList.add('disabled-link');
            elem.addEventListener('click', function (event) {
                if (elem.classList.contains('disabled-link')) {
                    event.preventDefault();
                }
            });

            elem.addEventListener('contextmenu', function (event) {
                event.preventDefault();
            });
        });

        window.addEventListener('load', function () {
            document.querySelectorAll('.fa-edit').forEach(function (elem) {
                elem.classList.remove('disabled-link');
            });
        });
    }

    function initDatatableActions() {
        var datatable = document.getElementById('datatable');
        if (!datatable) {
            return;
        }

        var modelo = getModelo();

        datatable.addEventListener('click', function (event) {
            var target = event.target;
            if (!(target instanceof Element)) {
                return;
            }

            var qrIcon = target.closest('a.QR');
            if (qrIcon) {
                event.preventDefault();
                var baseUrl = (qrIcon.getAttribute('href') || '') + '/';
                var posicion = window.prompt('Introdueix posició de la primera etiqueta', 1);
                window.location.href = baseUrl + posicion;
                return;
            }

            var editIcon = target.closest('.fa-edit');
            if (editIcon) {
                event.preventDefault();
                var row = editIcon.closest('tr');
                var id = row ? row.getAttribute('id') : '';
                if (!id) {
                    return;
                }

                var editAnchor = editIcon.closest('a');
                setModalAttrs(editAnchor, 'create');
                showModal('create');

                apiGet('/api/' + modelo + '/' + id + '/edit')
                    .then(function (res) {
                        fillEditForm(res.data, id);
                    })
                    .catch(function (error) {
                        console.log(error);
                    });

                return;
            }

            var eyeIcon = target.closest('.fa-eye');
            if (eyeIcon) {
                event.preventDefault();
                var rowEye = eyeIcon.closest('tr');
                var idEye = rowEye ? rowEye.getAttribute('id') : '';
                if (!idEye) {
                    return;
                }

                var eyeAnchor = eyeIcon.closest('a');
                setModalAttrs(eyeAnchor, 'show');
                showModal('show');

                apiGet('/api/' + modelo + '/' + idEye)
                    .then(function (res) {
                        renderShowData(res.data);
                    })
                    .catch(function (error) {
                        console.log(error);
                    });
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        initCreateHiddenHandler();
        initCreateButton();
        initDangerAlertModalRestore();
        initAuxClickGuard();
        initEditTemporaryDisable();
        initDatatableActions();
    });
})();
