'use strict';

(function () {
    function trim(value) {
        return (value || '').toString().trim();
    }

    function apiAuthOptions(extraData) {
        var tokenNode = document.getElementById('_token');
        var legacyToken = trim(tokenNode ? tokenNode.textContent : '');
        var bearerMeta = document.querySelector('meta[name="user-bearer-token"]');
        var bearerToken = trim(bearerMeta ? bearerMeta.getAttribute('content') : '');
        var data = extraData ? Object.assign({}, extraData) : {};
        var headers = {};

        if (bearerToken) {
            headers.Authorization = 'Bearer ' + bearerToken;
        }
        if (legacyToken) {
            data.api_token = legacyToken;
        }

        return { headers: headers, data: data };
    }

    function toFormBody(data) {
        var params = new URLSearchParams();
        Object.keys(data || {}).forEach(function (key) {
            if (data[key] !== undefined && data[key] !== null) {
                params.append(key, String(data[key]));
            }
        });
        return params.toString();
    }

    function getOptionsMap() {
        if (window.options && typeof window.options === 'object') {
            return window.options;
        }
        if (window.seguimientoOptions && typeof window.seguimientoOptions === 'object') {
            return window.seguimientoOptions;
        }
        return {};
    }

    function hideError() {
        var errorBox = document.getElementById('error');
        if (errorBox) {
            errorBox.style.display = 'none';
        }
    }

    function showError(message) {
        var errorBox = document.getElementById('error');
        if (!errorBox) {
            return;
        }

        var messageNode = errorBox.firstElementChild;
        if (messageNode) {
            messageNode.textContent = message;
        }
        errorBox.style.display = 'block';
    }

    function clearEdition(row) {
        if (!row) {
            return;
        }

        row.querySelectorAll('span.editando').forEach(function (editingNode) {
            var original = editingNode.previousElementSibling;
            if (original) {
                original.style.display = '';
            }
            editingNode.remove();
        });
    }

    function cancelCurrentEdition(event) {
        if (event) {
            event.preventDefault();
            if (!confirm('Vas a descartar els canvis: ')) {
                return;
            }
        }

        document.querySelectorAll('tr').forEach(function (row) {
            clearEdition(row);
        });
    }

    function createEditWrapper() {
        var span = document.createElement('span');
        span.className = 'editando';
        return span;
    }

    function appendSelectOptions(select, optionSource, currentText) {
        Object.keys(optionSource || {}).forEach(function (key) {
            var option = document.createElement('option');
            option.value = key;
            option.textContent = optionSource[key];
            if (key === currentText || String(optionSource[key]) === currentText) {
                option.selected = true;
            }
            select.appendChild(option);
        });
    }

    function startEdition(row) {
        if (!row) {
            return;
        }

        cancelCurrentEdition();
        var optionsMap = getOptionsMap();

        row.querySelectorAll('td').forEach(function (cell) {
            var source = cell.firstElementChild;
            if (!source || source.tagName !== 'SPAN') {
                return;
            }

            source.style.display = 'none';
            var sourceClass = source.className;
            var sourceName = source.getAttribute('name') || '';
            var sourceText = trim(source.textContent);
            var wrapper = createEditWrapper();

            switch (sourceClass) {
                case 'none': {
                    wrapper.textContent = sourceText;
                    break;
                }
                case 'input': {
                    var input = document.createElement('input');
                    input.type = 'text';
                    input.className = 'form-control';
                    input.name = sourceName;
                    input.value = sourceText;
                    input.maxLength = 200;
                    wrapper.appendChild(input);
                    break;
                }
                case 'select': {
                    var select = document.createElement('select');
                    select.className = 'form-control';
                    select.name = sourceName;
                    select.id = sourceName;
                    appendSelectOptions(select, optionsMap[sourceName] || {}, sourceText);
                    wrapper.appendChild(select);
                    break;
                }
                case 'objselect': {
                    var objSelect = document.createElement('select');
                    objSelect.className = 'form-control';
                    objSelect.name = sourceName;
                    objSelect.id = sourceName;
                    appendSelectOptions(objSelect, optionsMap, sourceText);
                    wrapper.appendChild(objSelect);
                    break;
                }
                case 'textarea': {
                    var textarea = document.createElement('textarea');
                    textarea.className = 'form-control';
                    textarea.name = sourceName;
                    textarea.id = sourceName;
                    textarea.rows = 4;
                    textarea.value = source.innerHTML;
                    wrapper.appendChild(textarea);
                    break;
                }
                case 'botones': {
                    var okLink = document.createElement('a');
                    okLink.href = '#';
                    okLink.className = 'imgButton edit-ok';
                    okLink.innerHTML = '<i class="fa fa-check" alt="Aceptar" title="Aceptar"></i>';

                    var cancelLink = document.createElement('a');
                    cancelLink.href = '#';
                    cancelLink.className = 'imgButton edit-cancel';
                    cancelLink.innerHTML = '<i class="fa fa-close" alt="Cancelar" title="Cancelar"></i>';

                    wrapper.appendChild(okLink);
                    wrapper.appendChild(cancelLink);
                    break;
                }
                default:
                    wrapper.textContent = sourceText;
                    break;
            }

            source.insertAdjacentElement('afterend', wrapper);
        });
    }

    function saveEdition(event, row) {
        event.preventDefault();
        if (!row) {
            return;
        }

        var auth = apiAuthOptions();
        var datos = {};
        var id = row.getAttribute('id');
        var table = row.closest('table');
        var tabla = table ? table.getAttribute('name') : '';

        if (!id || !tabla) {
            return;
        }

        row.querySelectorAll('span.editando [name]').forEach(function (input) {
            datos[input.getAttribute('name')] = input.value;
        });

        if (!auth.headers.Authorization && auth.data.api_token) {
            datos.api_token = auth.data.api_token;
        }

        fetch('/api/' + tabla + '/' + id, {
            method: 'PUT',
            headers: Object.assign({}, auth.headers, {
                'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
            }),
            body: toFormBody(datos),
            credentials: 'same-origin'
        })
            .then(function (response) {
                return response.json().catch(function () {
                    return {};
                }).then(function (json) {
                    if (!response.ok) {
                        throw new Error(json.message || ('HTTP ' + response.status));
                    }
                    return json;
                });
            })
            .then(function (result) {
                if (!(result.message === 'OK' || result.success)) {
                    var optionsMap = getOptionsMap();
                    showError(optionsMap.error || result.message || 'No s’han pogut guardar els canvis');
                    return;
                }

                var optionsMap = getOptionsMap();
                row.querySelectorAll('span.editando').forEach(function (editingNode) {
                    var original = editingNode.previousElementSibling;
                    if (!original) {
                        return;
                    }

                    if (original.className !== 'botones') {
                        var field = editingNode.querySelector('[name]');
                        if (field) {
                            if (original.className === 'select') {
                                var sourceName = original.getAttribute('name') || '';
                                var sourceOptions = optionsMap[sourceName] || {};
                                original.innerHTML = sourceOptions[field.value] || field.value;
                            } else if (original.className === 'textarea') {
                                original.innerHTML = field.value;
                            } else {
                                original.innerHTML = field.value;
                            }
                        }
                    }
                });

                hideError();
                clearEdition(row);
            })
            .catch(function (error) {
                window.console.log(error);
                showError(error.message || 'No s’han pogut guardar els canvis');
            });
    }

    document.addEventListener('DOMContentLoaded', function () {
        hideError();

        document.addEventListener('click', function (event) {
            var editTrigger = event.target.closest('.editGrupo');
            if (editTrigger) {
                event.preventDefault();
                startEdition(editTrigger.closest('tr'));
                return;
            }

            var deleteTrigger = event.target.closest('.delGrupo');
            if (deleteTrigger) {
                if (!confirm('Vas a esborrar la linea: ')) {
                    event.preventDefault();
                }
                return;
            }

            var cancelTrigger = event.target.closest('.edit-cancel');
            if (cancelTrigger) {
                cancelCurrentEdition(event);
                return;
            }

            var okTrigger = event.target.closest('.edit-ok');
            if (okTrigger) {
                saveEdition(event, okTrigger.closest('tr'));
            }
        });
    });
})();
