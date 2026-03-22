'use strict';

(function () {
    var PROCEDENCIAS = ['Desconocida', 'Dotación', 'Compra', 'Donación'];
    var ADMINISTRADOR = 11;
    var MANTENIMIENTO = 7;
    var mesesCaduca = 6;
    var dataTableInstance = null;

    function byId(id) {
        return document.getElementById(id);
    }

    function trim(value) {
        return (value || '').toString().trim();
    }

    function getRoleModulo(divisor) {
        var rolElement = byId('rol');
        var rol = parseInt(trim(rolElement ? rolElement.textContent : ''), 10);
        if (Number.isNaN(rol)) {
            return false;
        }
        return !(rol % divisor);
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

    function request(method, url, extraData, expectJson) {
        var auth = apiAuthOptions(extraData || {});
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
                    error.status = response.status;
                    error.statusText = response.statusText;
                    error.responseText = text;
                    throw error;
                });
            }

            if (expectJson === false) {
                return response.text();
            }
            return response.json();
        });
    }

    function showModal(id) {
        if (window.intranetUiHelpers) {
            window.intranetUiHelpers.showModal(id);
        }
    }

    function hideModal(id) {
        if (window.intranetUiHelpers) {
            window.intranetUiHelpers.hideModal(id);
        }
    }

    function getCellText(row, index) {
        var cell = row && row.children ? row.children[index] : null;
        return trim(cell ? cell.textContent : '');
    }

    function actionCellContent(admin, autorizado) {
        var html = '';
        if (admin) {
            html += "<a href=\"#\" class=\"edit\"><i class=\"fa fa-pencil\" title=\"Editar\"></i></a>";
            html += "<a href=\"#\" class=\"delete\"><i class=\"fa fa-trash\" title=\"Borrar\"></i></a>";
        }
        html += "<a href=\"#\" class=\"ver\"><i class=\"fa fa-eye\" title=\"Ver\"></i></a>";
        if (autorizado) {
            html += "<a href=\"#\" class=\"ubicacion\"><i class=\"fa fa-map-marker\" title=\"Cambiar ubicación\"></i></a>";
            html += "<a href=\"#\" class=\"estado\"><i class=\"fa fa-toggle-on\" title=\"Cambiar estado\"></i></a>";
        }
        return html;
    }

    function htmlDialog(dlgControls) {
        var html = '<form class="form-horizontal form-label-left">';
        dlgControls.forEach(function (control) {
            html += '<div class="form-group">';
            html += '<label class="control-label col-md-3 col-sm-3 col-xs-12" for="' + control.id.toLowerCase() + '">' + control.id + '</label>';
            html += '<div class="col-md-6 col-sm-6 col-xs-12">';
            if (control.type === 'input') {
                html += '<input type="number" id="' + control.id.toLowerCase() + '" ';
                if (control.val !== undefined) {
                    html += 'value="' + control.val + '" ';
                }
                html += 'class="form-control" />';
            } else if (control.type === 'select') {
                html += '<select id="' + control.id.toLowerCase() + '" class="form-control"></select>';
            } else if (control.type === 'textarea') {
                html += '<textarea id="' + control.id.toLowerCase() + '" class="form-control"></textarea>';
            } else if (control.type === 'span') {
                html += '<span id="' + control.id.toLowerCase() + '" class="form-control">';
                if (control.val !== undefined) {
                    html += control.val;
                }
                html += '</span>';
            }
            html += '</div></div>';
        });
        html += '</form>';
        return html;
    }

    function initDataTable(admin, autorizado) {
        var table = byId('datamaterial');
        if (!table) {
            return Promise.resolve(null);
        }
        var espai = trim(byId('search') ? byId('search').textContent : '');
        var article = trim(byId('article') ? byId('article').textContent : '');

        return request('GET', '/api/inventario/' + espai, {}, true).then(function (result) {
            var data = result && result.data ? result.data : [];
            var options = {
                data: data,
                columnDefs: [
                    { targets: [1], visible: false }
                ],
                searchCols: [
                    null,
                    { search: article },
                    null,
                    null,
                    null
                ],
                deferRender: true,
                dom: 'Bfrtip',
                buttons: ['print'],
                columns: [
                    { data: 'id' },
                    { data: 'articulo' },
                    { data: 'descripcion' },
                    { data: 'estado' },
                    { data: 'espacio' },
                    { data: null, defaultContent: actionCellContent(admin, autorizado) },
                    {
                        data: null,
                        render: function (row, type) {
                            var fechaUltimo = new Date(row.fechaultimoinventario);
                            var fechaCaduca = new Date();
                            fechaCaduca.setMonth(fechaCaduca.getMonth() - mesesCaduca);
                            if (type === 'display') {
                                if (fechaUltimo > fechaCaduca) {
                                    return '<input type="checkbox" checked class="editor-active">';
                                }
                                return '<input type="checkbox" class="editor-active">';
                            }
                            return row;
                        }
                    }
                ],
                language: {
                    url: '/json/cattable.json'
                }
            };

            if (typeof window.DataTable === 'function') {
                dataTableInstance = new window.DataTable(table, options);
                return dataTableInstance;
            }
            var jq = window.$;
            if (jq && jq.fn && typeof jq.fn.DataTable === 'function') {
                dataTableInstance = jq(table).DataTable(options);
                return dataTableInstance;
            }
            return null;
        });
    }

    function openViewModal(idMaterial) {
        request('GET', '/api/material/' + idMaterial, {}, true).then(function (result) {
            var dlgControls = [
                { id: 'NumSerie', type: 'span', val: result.data.nserieprov },
                { id: 'Descripcion', type: 'span', val: result.data.descripcion },
                { id: 'Marca', type: 'span', val: result.data.marca },
                { id: 'Modelo', type: 'span', val: result.data.modelo },
                { id: 'Procedencia', type: 'span', val: PROCEDENCIAS[result.data.procedencia] },
                { id: 'Estado', type: 'span', val: result.data.estado },
                { id: 'Espacio', type: 'span', val: result.data.espacio },
                { id: 'Unidades', type: 'span', val: result.data.unidades },
                { id: 'Isbn', type: 'span', val: result.data.ISBN },
                { id: 'FechaUltimoInventario', type: 'span', val: result.data.fechaultimoinventario },
                { id: 'Tipo', type: 'span', val: result.data.tipo },
                { id: 'Proveedor', type: 'span', val: result.data.proveedor }
            ];
            var dialogo = byId('dialogo');
            if (!dialogo) {
                return;
            }
            var title = dialogo.querySelector('.modal-title');
            var body = dialogo.querySelector('.modal-body');
            var submit = dialogo.querySelector('.modal-footer button[type=submit]');
            var cancel = dialogo.querySelector('.modal-footer button[type=button]');

            if (title) {
                title.textContent = 'Ver Material';
            }
            if (body) {
                body.innerHTML = htmlDialog(dlgControls);
            }
            if (submit) {
                submit.style.display = 'none';
                submit.onclick = null;
            }
            if (cancel) {
                cancel.textContent = 'Cerrar';
            }
            showModal('dialogo');
        });
    }

    function getSelectedIdsFromRows() {
        var ids = [];
        document.querySelectorAll('#datamaterial tbody tr.selected').forEach(function (row) {
            var id = getCellText(row, 0);
            if (id) {
                ids.push(id);
            }
        });
        return ids;
    }

    function getSelectedIds() {
        if (dataTableInstance && typeof dataTableInstance.rows === 'function') {
            try {
                var rows = dataTableInstance.rows('.selected').data();
                var ids = [];
                for (var i = 0; i < rows.length; i += 1) {
                    if (rows[i] && rows[i].id !== undefined) {
                        ids.push(rows[i].id);
                    }
                }
                return ids;
            } catch (error) {
                return getSelectedIdsFromRows();
            }
        }
        return getSelectedIdsFromRows();
    }

    function openEditModal(config) {
        var dialogo = byId('dialogo');
        if (!dialogo) {
            return;
        }
        var title = dialogo.querySelector('.modal-title');
        var body = dialogo.querySelector('.modal-body');
        var submit = dialogo.querySelector('.modal-footer button[type=submit]');
        var cancel = dialogo.querySelector('.modal-footer button[type=button]');

        if (title) {
            title.textContent = config.title;
        }
        if (body) {
            body.innerHTML = htmlDialog(config.controls);
        }
        if (cancel) {
            cancel.textContent = 'Cancelar';
        }
        if (submit) {
            submit.style.display = '';
            submit.onclick = function () {
                config.onSubmit();
                hideModal('dialogo');
            };
        }
        showModal('dialogo');
        if (typeof config.afterRender === 'function') {
            config.afterRender();
        }
    }

    function handleTableClick(event, admin, autorizado) {
        var row = event.target.closest('tr');
        if (!row) {
            return;
        }

        var firstCell = event.target.closest('td:first-child');
        if (firstCell && row.parentElement && row.parentElement.tagName.toLowerCase() === 'tbody') {
            row.classList.toggle('selected');
        }

        var anchor = event.target.closest('a');
        if (!anchor) {
            return;
        }
        var idMaterial = getCellText(row, 0);
        if (!idMaterial) {
            return;
        }

        if (anchor.classList.contains('ver')) {
            event.preventDefault();
            openViewModal(idMaterial);
            return;
        }

        if (anchor.classList.contains('incidencia')) {
            anchor.setAttribute('href', '/material/' + idMaterial + '/incidencia');
            return;
        }

        if (admin && anchor.classList.contains('delete')) {
            var info = '\n';
            var table = row.closest('table');
            var titles = table ? table.querySelectorAll('thead th') : [];
            Array.prototype.forEach.call(row.children, function (cell, i) {
                var html = trim(cell.innerHTML);
                if (html.length > 0) {
                    var title = titles[i] ? trim(titles[i].textContent) : '';
                    info += ' - ' + title + ': ' + cell.innerHTML + '\n';
                }
            });
            if (window.confirm('Vas a donar de baixa el material:' + info)) {
                anchor.setAttribute('href', '/material/' + idMaterial + '/delete');
            } else {
                event.preventDefault();
            }
            return;
        }

        if (admin && anchor.classList.contains('edit')) {
            anchor.setAttribute('href', '/inventario/' + idMaterial + '/edit');
            return;
        }

        if (!autorizado) {
            return;
        }

        if (anchor.classList.contains('ubicacion')) {
            event.preventDefault();
            var espacioCell = row.children[4];
            var espacioTabla = trim(espacioCell ? espacioCell.textContent : '');
            openEditModal({
                title: 'Canviar Ubicació',
                controls: [
                    { id: 'Espacios', type: 'select' },
                    { id: 'Explicacion', type: 'textarea' }
                ],
                onSubmit: function () {
                    var espacios = byId('espacios');
                    var explicacion = byId('explicacion');
                    request('PUT', '/api/material/cambiarUbicacion', {
                        id: idMaterial,
                        ubicacion: espacios ? espacios.value : '',
                        ubicacion_antes: espacioTabla,
                        explicacion: explicacion ? explicacion.value : ''
                    }, true).then(function (res) {
                        if (espacioCell) {
                            espacioCell.textContent = res.data.updated;
                        }
                    });
                },
                afterRender: function () {
                    request('GET', '/api/espacio', {}, true).then(function (result) {
                        var select = byId('espacios');
                        if (!select) {
                            return;
                        }
                        (result.data || []).forEach(function (item) {
                            var option = document.createElement('option');
                            option.value = item.aula;
                            option.textContent = item.descripcion;
                            if (item.aula === espacioTabla) {
                                option.selected = true;
                            }
                            select.appendChild(option);
                        });
                    });
                }
            });
            return;
        }

        if (anchor.classList.contains('estado')) {
            event.preventDefault();
            var estadoCell = row.children[3];
            var estadoMaterial = trim(estadoCell ? estadoCell.textContent : '');
            openEditModal({
                title: 'Donar de Baixa',
                controls: [
                    { id: 'Explicacion', type: 'textarea' }
                ],
                onSubmit: function () {
                    var explicacionEstado = byId('explicacion');
                    request('PUT', '/api/material/cambiarEstado', {
                        id: idMaterial,
                        estado: 3,
                        estado_antes: estadoMaterial,
                        explicacion: explicacionEstado ? explicacionEstado.value : ''
                    }, true).then(function (res) {
                        if (estadoCell) {
                            estadoCell.textContent = res.data.updated;
                        }
                    });
                }
            });
        }
    }

    function handleInventoryChange(event, autorizado) {
        var checkbox = event.target;
        if (!autorizado || !checkbox.classList.contains('editor-active')) {
            return;
        }
        var row = checkbox.closest('tr');
        var idMaterial = getCellText(row, 0);
        var inventariado = checkbox.checked;
        request('PUT', '/api/material/cambiarInventario', {
            id: idMaterial,
            inventario: inventariado
        }, false).catch(function () {
            checkbox.checked = !inventariado;
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        var admin = getRoleModulo(ADMINISTRADOR);
        var autorizado = getRoleModulo(MANTENIMIENTO);

        initDataTable(admin, autorizado);

        var table = byId('datamaterial');
        if (table) {
            table.addEventListener('click', function (event) {
                handleTableClick(event, admin, autorizado);
            });
            table.addEventListener('change', function (event) {
                handleInventoryChange(event, autorizado);
            });
        }

        var printButton = byId('printCodeBar');
        if (printButton) {
            printButton.addEventListener('click', function (event) {
                event.preventDefault();
                var ids = getSelectedIds();
                if (ids.length > 0) {
                    var idList = byId('idList');
                    var printForm = byId('printCodeBars');
                    if (idList) {
                        idList.value = ids.join(',');
                    }
                    if (printForm) {
                        printForm.submit();
                    }
                } else {
                    window.alert('Has de seleccionar primer les etiquetes polsant sobre la seua clau');
                }
            });
        }
    });
})();
