'use strict';

(function () {
    var state = {
        id: null,
        col: null,
        list: null,
        tipo: null,
        contactType: null
    };

    var CONTACT_RESULTS = {
        telefonada: [
            'Contactat',
            'No contesta',
            'Pendent de resposta',
            'Tornar a telefonar'
        ],
        correu: [
            'Correu enviat',
            'Resposta rebuda',
            'Pendent de resposta',
            'Cal reenviar'
        ],
        visita: [
            'Visita acordada',
            'Visita realitzada',
            'Pendent de concretar',
            'Visita ajornada'
        ],
        reunio: [
            'Reunió acordada',
            'Reunió realitzada',
            'Pendent de convocar',
            'Reunió ajornada'
        ],
        seguiment: [
            'Seguiment fet',
            'Pendent de resposta',
            'Cal tornar a contactar',
            'Seguiment tancat'
        ]
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

    function resetDialogForm() {
        var formDialogo = document.getElementById('formDialogo');
        if (!formDialogo) {
            return;
        }

        formDialogo.reset();

        ['explicacion', 'resultat', 'observacions', 'proxima_accio', 'data_prevista'].forEach(function (fieldName) {
            if (formDialogo[fieldName]) {
                formDialogo[fieldName].value = '';
            }
        });
    }

    function setContactType(contactType) {
        var select = document.getElementById('contact_type');
        if (select) {
            select.value = contactType || 'telefonada';
        }
        setResultOptions(contactType || 'telefonada');
    }

    function setResultOptions(contactType, selectedValue) {
        var select = document.getElementById('resultat');
        var options = CONTACT_RESULTS[contactType] || [];
        var resolvedSelectedValue = trim(selectedValue);

        if (!select) {
            return;
        }

        select.innerHTML = '';

        var placeholder = document.createElement('option');
        placeholder.value = '';
        placeholder.textContent = 'Selecciona resultat';
        placeholder.disabled = true;
        placeholder.selected = resolvedSelectedValue === '';
        select.appendChild(placeholder);

        options.forEach(function (value) {
            var option = document.createElement('option');
            option.value = value;
            option.textContent = value;
            option.selected = value === resolvedSelectedValue;
            select.appendChild(option);
        });

        if (resolvedSelectedValue !== '' && options.indexOf(resolvedSelectedValue) === -1) {
            var customOption = document.createElement('option');
            customOption.value = resolvedSelectedValue;
            customOption.textContent = resolvedSelectedValue;
            customOption.selected = true;
            select.appendChild(customOption);
        }
    }

    function fillContactDialog(contact) {
        var formDialogo = document.getElementById('formDialogo');
        if (!formDialogo || !contact) {
            return;
        }

        if (formDialogo.contact_type) {
            formDialogo.contact_type.value = contact.contact_type || 'telefonada';
        }
        setResultOptions(contact.contact_type || 'telefonada', contact.resultat || '');
        if (formDialogo.resultat) {
            formDialogo.resultat.value = contact.resultat || '';
        }
        if (formDialogo.observacions) {
            formDialogo.observacions.value = contact.observacions || '';
        }
        if (formDialogo.proxima_accio) {
            formDialogo.proxima_accio.value = contact.proxima_accio || '';
        }
        if (formDialogo.data_prevista) {
            formDialogo.data_prevista.value = contact.data_prevista || '';
        }
        if (formDialogo.explicacion) {
            formDialogo.explicacion.value = contact.comentari || '';
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

    function appendStructuredActivity(list, activity) {
        if (!list || !activity) {
            return;
        }

        var emptyState = list.querySelector('.js-empty-activity');
        if (emptyState) {
            emptyState.remove();
        }

        var icon = 'book';
        var label = activity.document || '';
        var createdAt = activity.created_at || '';

        if ((activity.action || '') === 'phone') {
            icon = 'phone';
        } else if ((activity.action || '') === 'email') {
            icon = 'envelope';
        } else if ((activity.action || '') === 'visita') {
            icon = 'car';
        } else if ((activity.action || '') === 'review') {
            icon = 'users';
        }

        var html = "<small><a href='#' class='small dragable' id='" + activity.id
            + "' draggable='draggable'><em class='fa "
            + (activity.comentari ? 'fa-plus' : 'fa-minus')
            + "'></em> " + createdAt + " <em class='fa fa-" + icon + "'></em> "
            + label + "</a></small><br/>";

        list.insertAdjacentHTML('beforeend', html);

        var lastLink = document.getElementById(String(activity.id));
        if (lastLink) {
            configureDraggable(lastLink);
        }
    }

    function formatContactDate(dateValue) {
        if (!dateValue) {
            return '';
        }

        var date = new Date(dateValue);
        if (Number.isNaN(date.getTime())) {
            return String(dateValue);
        }

        return date.toLocaleString('ca-ES', {
            day: '2-digit',
            month: '2-digit',
            year: 'numeric',
            hour: '2-digit',
            minute: '2-digit'
        });
    }

    function updateCardLastContact(list, activity) {
        var card = list ? list.closest('.profile_view') : null;
        var row = card ? card.querySelector('.js-last-contact-row') : null;
        var value = row ? row.querySelector('.js-last-contact-value') : null;
        var days = row ? row.querySelector('.js-last-contact-days') : null;

        if (!row || !value) {
            return;
        }

        row.classList.remove('text-danger');
        row.classList.add('text-muted');
        value.textContent = formatContactDate(activity.created_at || new Date().toISOString());
        if (days) {
            days.textContent = '· fa 0 dia(es)';
        }
    }

    function updateCardNextStep(list, activity) {
        var card = list ? list.closest('.profile_view') : null;
        var row = card ? card.querySelector('.js-next-step-row') : null;
        var value = row ? row.querySelector('.js-next-step-value') : null;
        var date = row ? row.querySelector('.js-next-step-date') : null;
        var nextStep = trim(activity.proxima_accio);
        var plannedDate = trim(activity.data_prevista);

        if (!row || !value || !date) {
            return;
        }

        if (nextStep === '') {
            row.style.display = 'none';
            value.textContent = '';
            date.textContent = '';
            return;
        }

        row.style.display = '';
        value.textContent = nextStep;
        date.textContent = plannedDate ? ('· ' + plannedDate) : '';
    }

    function parseDateOnly(dateValue) {
        if (!dateValue) {
            return null;
        }

        var parsed = new Date(dateValue);
        if (Number.isNaN(parsed.getTime())) {
            return null;
        }

        parsed.setHours(0, 0, 0, 0);
        return parsed;
    }

    function resolveFollowupState(activity) {
        var result = trim(activity.resultat).toLowerCase();
        var nextStep = trim(activity.proxima_accio);
        var plannedDate = parseDateOnly(activity.data_prevista);
        var today = new Date();

        today.setHours(0, 0, 0, 0);

        var status = {
            key: 'sense_seguiment',
            label: 'Sense seguiment',
            className: 'bg-secondary'
        };
        var urgency = {
            key: 'cap',
            label: '',
            className: 'bg-secondary'
        };

        if (activity.created_at) {
            status = {
                key: 'tancat',
                label: 'Seguiment tancat',
                className: 'bg-success'
            };

            if (result.indexOf('pendent de resposta') !== -1) {
                status = {
                    key: 'pendent_resposta',
                    label: 'Pendent de resposta',
                    className: 'bg-warning text-dark'
                };
            } else if (nextStep !== '') {
                status = {
                    key: 'en_curs',
                    label: 'En curs',
                    className: 'bg-info text-dark'
                };
            } else if (
                result.indexOf('tancat') !== -1
                || result.indexOf('seguiment fet') !== -1
                || result.indexOf('resposta rebuda') !== -1
                || result.indexOf('visita realitzada') !== -1
                || result.indexOf('reunió realitzada') !== -1
                || result.indexOf('contactat') !== -1
            ) {
                status = {
                    key: 'tancat',
                    label: 'Seguiment tancat',
                    className: 'bg-success'
                };
            }
        }

        if (plannedDate) {
            var thisWeek = new Date(today);
            thisWeek.setDate(thisWeek.getDate() + 7);

            if (plannedDate < today) {
                urgency = {
                    key: 'vençut',
                    label: 'Vençut',
                    className: 'bg-danger'
                };
            } else if (plannedDate <= thisWeek) {
                urgency = {
                    key: 'esta_setmana',
                    label: 'Esta setmana',
                    className: 'bg-warning text-dark'
                };
            }
        }

        return {
            status: status,
            urgency: urgency
        };
    }

    function updateCardFollowupBadges(list, activity) {
        var card = list ? list.closest('.mis-colaboraciones-card') : null;
        var statusBadge = card ? card.querySelector('.js-followup-status-badge') : null;
        var urgencyBadge = card ? card.querySelector('.js-followup-urgency-badge') : null;
        var state = resolveFollowupState(activity);

        if (!card || !statusBadge || !urgencyBadge) {
            return;
        }

        card.setAttribute('data-followup-status', state.status.key);
        card.setAttribute('data-followup-urgency', state.urgency.key);

        statusBadge.className = 'badge ' + state.status.className + ' js-followup-status-badge';
        statusBadge.textContent = state.status.label;

        if (state.urgency.label) {
            urgencyBadge.style.display = '';
            urgencyBadge.className = 'badge ' + state.urgency.className + ' js-followup-urgency-badge';
            urgencyBadge.textContent = state.urgency.label;
        } else {
            urgencyBadge.style.display = 'none';
            urgencyBadge.className = 'badge js-followup-urgency-badge';
            urgencyBadge.textContent = '';
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
        var jq = window.$;
        if (!jq || !jq.fn || typeof jq.fn.datetimepicker !== 'function') {
            return;
        }

        var metaLocale = document.querySelector('meta[name="app-locale"]');
        var pageLocale = ((metaLocale ? metaLocale.getAttribute('content') : '') || document.documentElement.lang || 'es')
            .toLowerCase()
            .split('-')[0];
        var pickerLocale = pageLocale === 'en' ? 'en' : (pageLocale === 'ca' ? 'ca' : 'es');
        var dateFormat = pageLocale === 'en' ? 'MM/DD/YYYY' : 'DD/MM/YYYY';
        var dateTimeFormat = pageLocale === 'en' ? 'MM/DD/YYYY h:mm A' : 'DD/MM/YYYY HH:mm';

        if (typeof window.moment !== 'undefined' && typeof window.moment.locale === 'function') {
            window.moment.locale(pickerLocale);
        }

        jq('input[type=text].datetime').datetimepicker({
            sideBySide: true,
            locale: pickerLocale,
            format: dateTimeFormat,
            stepping: 15
        });
        jq('input[type=text].time').datetimepicker({
            sideBySide: true,
            locale: pickerLocale,
            format: 'HH:mm',
            stepping: 15
        });
        jq('input[type=text].date').datetimepicker({
            sideBySide: true,
            locale: pickerLocale,
            format: dateFormat
        });
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
            var resolveBtn = event.target.closest('.resolve');
            if (resolveBtn) {
                event.preventDefault();
                var resolveCol = resolveBtn.closest('.well');
                request('GET', '/api/colaboracion/' + resolveCol.id + '/resolve', {}, true).then(function () {
                    hideElement(resolveBtn);
                    showElement(findActionButton(resolveBtn, '.unauthorize'));
                    showElement(findActionButton(resolveBtn, '.refuse'));
                    setColaboracionBorder(resolveCol, '#1abb9c');
                    moveProfileToTab(resolveBtn, 'tab_colabora');
                });
                return;
            }

            var refuseBtn = event.target.closest('.refuse');
            if (refuseBtn) {
                event.preventDefault();
                var refuseCol = refuseBtn.closest('.well');
                request('GET', '/api/colaboracion/' + refuseCol.id + '/refuse', {}, true).then(function () {
                    hideElement(refuseBtn);
                    showElement(findActionButton(refuseBtn, '.resolve'));
                    showElement(findActionButton(refuseBtn, '.unauthorize'));
                    setColaboracionBorder(refuseCol, '#90111a');
                    moveProfileToTab(refuseBtn, 'tab_pendiente');
                });
                return;
            }

            var unauthorizeBtn = event.target.closest('.unauthorize');
            if (unauthorizeBtn) {
                event.preventDefault();
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
                return;
            }

            var switchBtn = event.target.closest('.switch');
            if (switchBtn) {
                event.preventDefault();
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
                return;
            }

            var telefonicoBtn = event.target.closest('.telefonico');
            if (telefonicoBtn) {
                event.preventDefault();
                var profileTelefonico = telefonicoBtn.closest('.profile_view');
                state.col = profileTelefonico ? profileTelefonico.id : null;
                state.list = profileTelefonico ? profileTelefonico.querySelector('.listActivity') : null;
                state.tipo = 'contacte';
                state.contactType = 'telefonada';
                resetDialogForm();
                setContactType('telefonada');
                openDialog();
                return;
            }

            var bookBtn = event.target.closest('.book');
            if (bookBtn) {
                event.preventDefault();
                var profileBook = bookBtn.closest('.profile_view');
                state.col = profileBook ? profileBook.id : null;
                state.list = profileBook ? profileBook.querySelector('.listActivity') : null;
                state.tipo = 'contacte';
                state.contactType = 'seguiment';
                resetDialogForm();
                setContactType('seguiment');
                openDialog();
                return;
            }

            var smallBtn = event.target.closest('.small');
            if (smallBtn) {
                event.preventDefault();
                state.id = smallBtn.id;
                state.tipo = 'seguimiento';
                resetDialogForm();
                request('GET', '/api/colaboracion/contact/' + state.id, {}, true)
                    .then(function (result) {
                        if (result && result.data) {
                            fillContactDialog(result.data);
                        }
                        openDialog();
                    })
                    .catch(function () {
                        window.console.log('Error al carregar el contacte.');
                    });
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

            var plusBtn = event.target.closest('.fa-plus');
            if (plusBtn) {
                var profilePlus = plusBtn.closest('.profile_view');
                var profileId = profilePlus ? profilePlus.id : null;
                if (!profileId) {
                    return;
                }

                var formAddAlumno = document.getElementById('formAddAlumno');
                var idColaboracion = document.getElementById('idColaboracion');
                var instructorSelect = document.getElementById('idInstructor');

                if (formAddAlumno) {
                    formAddAlumno.setAttribute('action', '/fct/fctalumnoCreate');
                }
                if (idColaboracion) {
                    idColaboracion.value = profileId;
                }

                request('GET', '/api/colaboracion/instructores/' + profileId, {}, true)
                    .then(function (result) {
                        if (!instructorSelect) {
                            return;
                        }
                        instructorSelect.innerHTML = '';
                        (result && result.data ? result.data : []).forEach(function (value) {
                            var option = document.createElement('option');
                            option.value = value.dni;
                            option.textContent = value.name + ' ' + value.surnames;
                            instructorSelect.appendChild(option);
                        });
                    })
                    .catch(function () {
                        window.console.log('La solicitud no se ha podido completar.');
                    });
            }
        });

        var formDialogo = document.getElementById('formDialogo');
        if (formDialogo) {
            if (formDialogo.contact_type) {
                formDialogo.contact_type.addEventListener('change', function () {
                    setResultOptions(this.value || 'telefonada');
                });
            }

            formDialogo.addEventListener('submit', function (event) {
                event.preventDefault();
                var contactType = formDialogo.contact_type ? formDialogo.contact_type.value : (state.contactType || 'telefonada');
                var resultat = formDialogo.resultat ? formDialogo.resultat.value : '';
                var observacions = formDialogo.observacions ? formDialogo.observacions.value : '';
                var proximaAccio = formDialogo.proxima_accio ? formDialogo.proxima_accio.value : '';
                var dataPrevista = formDialogo.data_prevista ? formDialogo.data_prevista.value : '';

                if (state.tipo === 'contacte') {
                    request('POST', '/api/colaboracion/' + state.col + '/contact', {
                        contact_type: contactType,
                        resultat: resultat,
                        observacions: observacions,
                        proxima_accio: proximaAccio,
                        data_prevista: dataPrevista
                    }, true)
                        .then(function (result) {
                            appendStructuredActivity(state.list, result.data);
                            updateCardLastContact(state.list, result.data);
                            updateCardNextStep(state.list, result.data);
                            updateCardFollowupBadges(state.list, result.data);
                            closeDialog();
                        })
                        .catch(function () {
                            window.console.log('No s\'ha pogut guardar el contacte.');
                            closeDialog();
                        });
                    return;
                }

                if (state.tipo === 'seguimiento') {
                    if (formDialogo.contact_type) {
                        request('PUT', '/api/colaboracion/contact/' + state.id, {
                            contact_type: contactType,
                            resultat: resultat,
                            observacions: observacions,
                            proxima_accio: proximaAccio,
                            data_prevista: dataPrevista
                        }, true)
                            .then(function () {
                                closeDialog();
                            })
                            .catch(function () {
                                window.console.log('Error al modificar el contacte.');
                                closeDialog();
                            });
                        return;
                    }
                }
            });
        }
    });
})();
