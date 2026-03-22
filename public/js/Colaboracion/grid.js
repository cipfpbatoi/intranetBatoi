'use strict';

(function () {
    var state = {
        id: null,
        col: null,
        list: null,
        tipo: null
    };

    var MAX_RETRIES = 40;
    var RETRY_DELAY_MS = 250;

    function trim(value) {
        return (value || '').toString().trim();
    }

    function getTableHelper() {
        return window.intranetDataTable || {};
    }

    function hasDataTables() {
        var tableHelper = getTableHelper();
        var hasV2 = typeof tableHelper.hasDataTableV2 === 'function' && tableHelper.hasDataTableV2();
        var hasJqDt = typeof tableHelper.hasJQueryDataTable === 'function' && tableHelper.hasJQueryDataTable();

        return hasV2 || hasJqDt;
    }

    function paintRow(row, statusText) {
        var stateText = trim(statusText).toLowerCase();

        row.classList.remove('bg-green', 'bg-red', 'bg-warning');

        if (stateText.indexOf('col·labora') !== -1 && stateText.indexOf('no col') === -1) {
            row.classList.add('bg-green');
            return;
        }

        if (stateText.indexOf('no col') !== -1) {
            row.classList.add('bg-red');
            return;
        }

        if (stateText.indexOf('no sé') !== -1 || stateText.indexOf('no contactada') !== -1) {
            row.classList.add('bg-warning');
        }
    }

    function paintExistingRows(table) {
        if (!table) {
            return;
        }

        table.querySelectorAll('tbody tr').forEach(function (row) {
            var stateCell = row.querySelector("span[name='Xestado']");
            var stateText = stateCell ? stateCell.textContent : '';
            paintRow(row, stateText);
        });
    }

    function initGrid() {
        var tableHelper = getTableHelper();
        var table = document.getElementById('datatable');

        if (!table) {
            return true;
        }

        paintExistingRows(table);

        if (!hasDataTables()) {
            return false;
        }

        if (typeof tableHelper.isInitialized === 'function' && tableHelper.isInitialized(table)) {
            return true;
        }

        table.style.visibility = 'hidden';

        var dataTable = null;
        var options = {
            language: { url: '/json/cattable.json' },
            deferRender: true,
            responsive: true,
            autoWidth: false,
            rowCallback: function (row, data) {
                paintRow(row, data && data[3] ? data[3] : '');
            },
            columnDefs: [
                { responsivePriority: 1, targets: 0 },
                { responsivePriority: 1, targets: -1 },
                { responsivePriority: 2, targets: 3 }
            ],
            initComplete: function () {
                if (dataTable && dataTable.columns && typeof dataTable.columns.adjust === 'function') {
                    dataTable.columns.adjust();
                }
                table.style.visibility = 'visible';
            }
        };

        dataTable = typeof tableHelper.init === 'function'
            ? tableHelper.init(table, options)
            : null;

        if (!dataTable) {
            table.style.visibility = 'visible';
            return true;
        }

        table.addEventListener('draw.dt', function () {
            dataTable.columns.adjust();
        });

        table.addEventListener('responsive-resize.dt', function () {
            dataTable.columns.adjust();
        });

        window.addEventListener('resize', function () {
            dataTable.columns.adjust();
        });

        return true;
    }

    function bootGrid(retriesLeft) {
        if (initGrid()) {
            return;
        }

        if (retriesLeft <= 0) {
            console.warn('DataTables no està disponible: Colaboracion/grid.js no s’inicialitza.');
            return;
        }

        window.setTimeout(function () {
            bootGrid(retriesLeft - 1);
        }, RETRY_DELAY_MS);
    }

    function getLegacyToken() {
        var tokenElement = document.getElementById('_token');
        return tokenElement ? trim(tokenElement.textContent) : '';
    }

    function getBearerToken() {
        var meta = document.querySelector('meta[name="user-bearer-token"]');
        return meta ? trim(meta.getAttribute('content')) : '';
    }

    function apiAuthOptions(extraData) {
        var data = extraData ? Object.assign({}, extraData) : {};
        var headers = {};
        var bearerToken = getBearerToken();
        var legacyToken = getLegacyToken();

        if (bearerToken) {
            headers.Authorization = 'Bearer ' + bearerToken;
        }
        if (legacyToken) {
            data.api_token = legacyToken;
        }

        return { headers: headers, data: data };
    }

    function withQueryParams(url, params) {
        var query = new URLSearchParams(params || {}).toString();
        if (!query) {
            return url;
        }
        return url + (url.indexOf('?') === -1 ? '?' : '&') + query;
    }

    function toFormBody(data) {
        var params = new URLSearchParams();
        Object.keys(data || {}).forEach(function (key) {
            var value = data[key];
            if (value !== undefined && value !== null) {
                params.append(key, String(value));
            }
        });
        return params.toString();
    }

    function request(method, url, extraData, asJson) {
        var auth = apiAuthOptions(extraData);
        var options = {
            method: method,
            headers: Object.assign({}, auth.headers),
            credentials: 'same-origin'
        };
        var finalUrl = url;

        if (method === 'GET' || method === 'DELETE') {
            finalUrl = withQueryParams(url, auth.data);
        } else {
            options.headers['Content-Type'] = 'application/x-www-form-urlencoded; charset=UTF-8';
            options.body = toFormBody(auth.data);
        }

        return fetch(finalUrl, options).then(function (response) {
            if (!response.ok) {
                return response.text().then(function (text) {
                    var error = new Error('HTTP ' + response.status);
                    error.responseText = text;
                    throw error;
                });
            }

            if (asJson === false) {
                return response.text();
            }

            var contentType = response.headers.get('content-type') || '';
            if (contentType.indexOf('application/json') !== -1) {
                return response.json();
            }
            return response.text();
        });
    }

    function showElement(element) {
        if (element) {
            element.style.display = '';
        }
    }

    function hideElement(element) {
        if (element) {
            element.style.display = 'none';
        }
    }

    function findActionButton(source, className) {
        var container = source ? source.parentElement : null;
        return container ? container.querySelector(className) : null;
    }

    function setColaboracionBorder(colaboracion, color) {
        if (!colaboracion) {
            return;
        }
        colaboracion.style.borderColor = color;
        colaboracion.style.borderWidth = 'medium';
    }

    function moveProfileToTab(sourceElement, tabId) {
        var profile = sourceElement ? sourceElement.closest('.profile_view') : null;
        var tab = document.getElementById(tabId);
        if (!profile || !tab || profile.parentElement === tab) {
            return;
        }
        tab.appendChild(profile);
    }

    function closeDialog() {
        if (window.intranetUiHelpers) {
            window.intranetUiHelpers.hideModal('dialogo');
        }
    }

    function openDialog() {
        if (window.intranetUiHelpers) {
            window.intranetUiHelpers.showModal('dialogo');
        }
    }

    function appendActivity(list, activityId, iconClass) {
        if (!list) {
            return;
        }

        var day = new Date();
        var month = day.getMonth() + 1;
        var html = "<small><a href='#' class='small dragable' id='" + activityId
            + "' draggable='draggable'><em class='fa fa-plus'></em> "
            + day.getDate() + '/' + month + " <em class='fa " + iconClass + "'></em></a></small><br/>";
        list.insertAdjacentHTML('beforeend', html);

        var lastLink = document.getElementById(String(activityId));
        if (lastLink) {
            configureDraggable(lastLink);
        }
    }

    function configureDraggable(item) {
        if (!item) {
            return;
        }

        item.setAttribute('draggable', 'draggable');
        item.addEventListener('dragstart', function (event) {
            event.dataTransfer.setData('text/plain', event.target.id);
        });
    }

    function configureDropTarget(item) {
        item.addEventListener('dragover', function (event) {
            event.preventDefault();
        });

        item.addEventListener('drop', function (event) {
            event.preventDefault();
            var activityId = event.dataTransfer.getData('text/plain');
            var newFct = event.currentTarget;
            if (!window.confirm('Vas a moure esta evidencia a una altra FCT')) {
                return;
            }

            request('GET', '/api/activity/' + activityId + '/move/' + newFct.id, {}, true)
                .then(function () {
                    var activityNode = document.getElementById(activityId);
                    var wrapper = activityNode ? activityNode.parentElement : null;
                    var targetList = newFct.querySelector('.listActivity');
                    if (wrapper && targetList) {
                        targetList.appendChild(wrapper);
                    }
                })
                .catch(function (error) {
                    window.alert("La sol·licitut no s'ha pogut completar: " + (error.responseText || error.message));
                });
        });
    }

    function initDatePickers() {
        var datepickers = window.intranetDatepickers;
        if (!datepickers || typeof datepickers.init !== 'function') {
            return;
        }

        datepickers.init(document);
    }

    function handleResolveAction(resolveBtn) {
        var resolveCol = resolveBtn.closest('.well');
        request('GET', '/api/colaboracion/' + resolveCol.id + '/resolve', {}, true).then(function () {
            hideElement(resolveBtn);
            showElement(findActionButton(resolveBtn, '.unauthorize'));
            showElement(findActionButton(resolveBtn, '.refuse'));
            setColaboracionBorder(resolveCol, '#1abb9c');
            moveProfileToTab(resolveBtn, 'tab_colabora');
        });
    }

    function handleRefuseAction(refuseBtn) {
        var refuseCol = refuseBtn.closest('.well');
        request('GET', '/api/colaboracion/' + refuseCol.id + '/refuse', {}, true).then(function () {
            hideElement(refuseBtn);
            showElement(findActionButton(refuseBtn, '.resolve'));
            showElement(findActionButton(refuseBtn, '.unauthorize'));
            setColaboracionBorder(refuseCol, '#90111a');
            moveProfileToTab(refuseBtn, 'tab_pendiente');
        });
    }

    function handleUnauthorizeAction(unauthorizeBtn) {
        var unauthCol = unauthorizeBtn.closest('.well');
        request('GET', '/api/colaboracion/' + unauthCol.id + '/unauthorize', {}, true).then(function () {
            hideElement(unauthorizeBtn);
            if (!findActionButton(unauthorizeBtn, '.switch')) {
                showElement(findActionButton(unauthorizeBtn, '.resolve'));
                showElement(findActionButton(unauthorizeBtn, '.refuse'));
            }
            setColaboracionBorder(unauthCol, '#00aeef');
            moveProfileToTab(unauthorizeBtn, 'tab_pendiente');
        });
    }

    function handleSwitchAction(switchBtn) {
        var switchCol = switchBtn.closest('.well');
        request('GET', '/api/colaboracion/' + switchCol.id + '/switch', {}, true).then(function (result) {
            hideElement(switchBtn);
            showElement(findActionButton(switchBtn, '.resolve'));
            showElement(findActionButton(switchBtn, '.refuse'));
            setColaboracionBorder(switchCol, '#00aeef');

            var nameNode = switchCol.querySelector('.nombre');
            if (nameNode && result && result.data) {
                nameNode.textContent = [result.data.nombre, result.data.apellido1, result.data.apellido2].join(' ');
            }
            moveProfileToTab(switchBtn, 'tab_pendiente');
        });
    }

    function handleBookAction(bookBtn) {
        var profileBook = bookBtn.closest('.profile_view');
        state.col = profileBook ? profileBook.id : null;
        state.list = profileBook ? profileBook.querySelector('.listActivity') : null;
        state.tipo = 'book';
        openDialog();
    }

    function handleTelefonicoAction(telefonicoBtn) {
        var profileTelefonico = telefonicoBtn.closest('.profile_view');
        var fctNode = profileTelefonico ? profileTelefonico.querySelector('.fct') : null;
        state.id = fctNode ? fctNode.id : null;
        state.list = profileTelefonico ? profileTelefonico.querySelector('.listActivity') : null;
        state.tipo = 'telefonico';
        openDialog();
    }

    function submitPreparationActivity(explicacion) {
        return request('POST', '/api/colaboracion/' + state.col + '/book', { explicacion: explicacion }, true)
            .then(function (result) {
                appendActivity(state.list, result.data.id, 'fa-book');
                closeDialog();
            })
            .catch(function () {
                window.console.log('Només es pot un per dia');
                closeDialog();
            });
    }

    function submitFctBridgeActivity(explicacion) {
        return request('POST', '/api/fct/' + state.id + '/telefonico', { explicacion: explicacion }, true)
            .then(function (result) {
                appendActivity(state.list, result.data.id, 'fa-phone');
                closeDialog();
            })
            .catch(function () {
                window.console.log('Només es pot un per dia');
                closeDialog();
            });
    }

    function handlePreparationActions(event) {
        var resolveBtn = event.target.closest('.resolve');
        if (resolveBtn) {
            event.preventDefault();
            handleResolveAction(resolveBtn);
            return true;
        }

        var refuseBtn = event.target.closest('.refuse');
        if (refuseBtn) {
            event.preventDefault();
            handleRefuseAction(refuseBtn);
            return true;
        }

        var unauthorizeBtn = event.target.closest('.unauthorize');
        if (unauthorizeBtn) {
            event.preventDefault();
            handleUnauthorizeAction(unauthorizeBtn);
            return true;
        }

        var switchBtn = event.target.closest('.switch');
        if (switchBtn) {
            event.preventDefault();
            handleSwitchAction(switchBtn);
            return true;
        }

        var bookBtn = event.target.closest('.book');
        if (bookBtn) {
            event.preventDefault();
            handleBookAction(bookBtn);
            return true;
        }

        return false;
    }

    function handleFctBridgeActions(event) {
        var telefonicoBtn = event.target.closest('.telefonico');
        if (telefonicoBtn) {
            event.preventDefault();
            handleTelefonicoAction(telefonicoBtn);
            return true;
        }

        return false;
    }

    function hideInitialButtons() {
        var resolveInColabora = document.querySelectorAll('#tab_colabora .resolve');
        var unauthorizeInPendiente = document.querySelectorAll('#tab_pendiente .unauthorize');
        var bookInResta = document.querySelectorAll('#tab_resta .book');

        resolveInColabora.forEach(hideElement);
        unauthorizeInPendiente.forEach(hideElement);
        bookInResta.forEach(hideElement);
    }

    document.addEventListener('DOMContentLoaded', function () {
        bootGrid(MAX_RETRIES);
        hideInitialButtons();
        initDatePickers();

        document.querySelectorAll('.dragable').forEach(configureDraggable);
        document.querySelectorAll('.fct').forEach(configureDropTarget);

        document.addEventListener('click', function (event) {
            // Accions de preparació: continuen dins del domini de col·laboració.
            if (handlePreparationActions(event)) {
                return;
            }

            // Accions pont: el panell les invoca, però ja operen sobre FCT.
            if (handleFctBridgeActions(event)) {
                return;
            }

            var smallBtn = event.target.closest('.small');
            if (smallBtn) {
                event.preventDefault();
                state.id = smallBtn.id;
                state.tipo = 'seguimiento';
                request('GET', '/api/activity/' + state.id, {}, true)
                    .then(function (result) {
                        var explicacion = document.querySelector('#dialogo #explicacion');
                        if (explicacion && result && result.data) {
                            explicacion.value = result.data.comentari || '';
                        }
                    })
                    .catch(function () {
                        window.console.log('Error al buscarr');
                    });
                openDialog();
                return;
            }

            var minusBtn = event.target.closest('.fa-minus');
            if (minusBtn) {
                event.preventDefault();
                event.stopPropagation();
                var activityAnchor = minusBtn.closest('.small');
                var deleteId = activityAnchor ? activityAnchor.id : null;
                if (!deleteId || !window.confirm('Vas a esborrar esta evidencia')) {
                    return;
                }

                request('DELETE', '/api/activity/' + deleteId, {}, true).then(function () {
                    if (minusBtn.parentElement) {
                        minusBtn.parentElement.remove();
                    }
                });
                return;
            }

        });

        var formDialogo = document.getElementById('formDialogo');
        if (formDialogo) {
            formDialogo.addEventListener('submit', function (event) {
                event.preventDefault();
                var explicacion = formDialogo.explicacion ? formDialogo.explicacion.value : '';

                if (state.tipo === 'book') {
                    submitPreparationActivity(explicacion);
                    return;
                }

                if (state.tipo === 'telefonico') {
                    submitFctBridgeActivity(explicacion);
                    return;
                }

                if (state.tipo === 'seguimiento') {
                    request('PUT', '/api/activity/' + state.id, { comentari: explicacion }, true)
                        .then(function () {
                            closeDialog();
                        })
                        .catch(function () {
                            window.console.log('Error al modificar');
                            closeDialog();
                        });
                }
            });
        }
    });
})();
