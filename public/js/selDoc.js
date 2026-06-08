(function () {
    'use strict';

    function getHelpers() {
        return window.intranetUiHelpers || {};
    }

    function getApiAuth() {
        return window.intranetApiAuth || {};
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
    }

    function hideModal(id) {
        var helpers = getHelpers();
        if (typeof helpers.hideModal === 'function') {
            helpers.hideModal(id);
            return;
        }
    }

    function apiGet(url) {
        var apiAuth = getApiAuth();
        if (typeof apiAuth.apiGet === 'function') {
            return apiAuth.apiGet(url);
        }

        return Promise.reject(new Error('intranetApiAuth.apiGet no disponible'));
    }

    function storageKey(button) {
        return 'signatura.skipSelection.' + (button.getAttribute('data-skip-selection-option') || 'default');
    }

    function readStorage(key) {
        try {
            return window.localStorage.getItem(key);
        } catch (error) {
            return null;
        }
    }

    function writeStorage(key, value) {
        try {
            window.localStorage.setItem(key, value);
        } catch (error) {
            return;
        }
    }

    function createSkipSelectionOption(button) {
        if (!button || !button.hasAttribute('data-skip-selection-option')) {
            return;
        }

        var id = 'skipSelection' + button.getAttribute('data-skip-selection-option');
        if (document.getElementById(id)) {
            return;
        }

        var wrapper = document.createElement('label');
        wrapper.className = 'checkbox-inline signatura-skip-selection-option';
        wrapper.style.marginLeft = '8px';

        var checkbox = document.createElement('input');
        checkbox.type = 'checkbox';
        checkbox.id = id;
        checkbox.setAttribute('data-skip-selection-for', button.getAttribute('data-skip-selection-option'));
        checkbox.checked = readStorage(storageKey(button)) === '1';

        checkbox.addEventListener('change', function () {
            writeStorage(storageKey(button), checkbox.checked ? '1' : '0');
        });

        wrapper.appendChild(checkbox);
        wrapper.appendChild(document.createTextNode(' Enviar sense finestra de selecció'));
        button.insertAdjacentElement('afterend', wrapper);
    }

    function skipSelectionIsEnabled(button) {
        var option = button.getAttribute('data-skip-selection-option');
        var checkbox = option ? document.querySelector('[data-skip-selection-for="' + option + '"]') : null;

        return checkbox ? checkbox.checked : false;
    }

    function csrfToken(form) {
        var input = form ? form.querySelector('input[name="_token"]') : null;
        if (input) {
            return input.value;
        }

        var meta = document.querySelector('meta[name="csrf-token"]');
        return meta ? meta.getAttribute('content') : '';
    }

    function directSubmit(route, token, elements) {
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = route;
        form.style.display = 'none';

        var tokenInput = document.createElement('input');
        tokenInput.type = 'hidden';
        tokenInput.name = '_token';
        tokenInput.value = token;
        form.appendChild(tokenInput);

        elements.forEach(function (element) {
            var input = document.createElement('input');
            input.type = 'hidden';
            input.name = element.id;
            input.value = 'on';
            form.appendChild(input);
        });

        document.body.appendChild(form);
        form.submit();
    }

    function selectedByDefault(elements) {
        return elements.filter(function (element) {
            return element.marked || element.marked == null;
        });
    }

    function sendWithoutSelection(button) {
        var url = button.getAttribute('data-url') || '';
        var route = button.getAttribute('id') || '';
        var form = document.getElementById('formA3A');

        apiGet(url)
            .then(function (result) {
                var elements = selectedByDefault(result.data || []);
                if (elements.length === 0) {
                    handleSeleccionClick(button, { preventDefault: function () {} }, true);
                    return;
                }

                button.classList.add('disabled');
                button.setAttribute('aria-disabled', 'true');
                directSubmit(route, csrfToken(form), elements);
            })
            .catch(function () {
                console.log('La sol·licitud no s\'ha pogut completar.');
                handleSeleccionClick(button, { preventDefault: function () {} }, true);
            });
    }

    function handleSeleccionClick(button, event, forceModal) {
        event.preventDefault();
        if (!forceModal && skipSelectionIsEnabled(button)) {
            sendWithoutSelection(button);
            return;
        }

        setModalAttrs(button, 'A3A');
        openModal('A3A');

        var url = button.getAttribute('data-url') || '';
        var route = button.getAttribute('id') || '';
        var form = document.getElementById('formA3A');
        if (form) {
            form.setAttribute('action', route);
        }

        apiGet(url)
            .then(function (result) {
                if (typeof window.pintaTablaSeleccion === 'function') {
                    window.pintaTablaSeleccion(result.data, '#tableA3');
                }
            })
            .catch(function () {
                console.log('La solicitud no se ha podido completar.');
            });
    }

    function handleSubmitClick(button, event) {
        event.preventDefault();

        var checkAll = document.getElementById('checkall');
        if (checkAll) {
            checkAll.checked = false;
        }

        hideModal('signatura');
        setModalAttrs(button, 'loading');
        openModal('loading');

        var form = document.getElementById('formA3A');
        if (form) {
            form.submit();
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.seleccion').forEach(function (button) {
            createSkipSelectionOption(button);
            button.addEventListener('click', function (event) {
                handleSeleccionClick(button, event);
            });
        });

        document.querySelectorAll('#A3A .submit').forEach(function (button) {
            button.addEventListener('click', function (event) {
                handleSubmitClick(button, event);
            });
        });
    });
})();
