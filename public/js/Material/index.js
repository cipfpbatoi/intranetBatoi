'use strict';

(function () {
    var PROCEDENCIAS = ['Desconocida', 'Dotación', 'Compra', 'Donación'];
    var ESTADOS = ['Desconocido', 'Ok', 'Reparándose', 'Baja'];
    var MANTENIMIENTO = 7;
    var mesesCaduca = 6;

    function byId(id) {
        return document.getElementById(id);
    }

    function trim(value) {
        return (value || '').toString().trim();
    }

    function getAuthorized() {
        var rolElement = byId('rol');
        var rol = parseInt(trim(rolElement ? rolElement.textContent : ''), 10);
        if (Number.isNaN(rol)) {
            return false;
        }
        return !(rol % MANTENIMIENTO);
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

    function actionCellContent(autorizado) {
        var html = '';
        if (autorizado) {
            html += "<a href=\"#\" class=\"delete\"><i class=\"fa fa-trash\" title=\"Borrar\"></i></a>";
            html += "<a href=\"#\" class=\"edit\"><i class=\"fa fa-pencil\" title=\"Editar\"></i></a>";
            html += "<a href=\"#\" class=\"copy\"><i class=\"fa fa-copy\" title=\"Copiar\"></i></a>";
        }
        html += "<a href=\"#\" class=\"incidencia\"><i class=\"fa fa-wrench\" title=\"Crear incidencia\"></i></a>";
        html += "<a href=\"#\" class=\"ver\"><i class=\"fa fa-eye\" title=\"Ver\"></i></a>";
        if (autorizado) {
            html += "<a href=\"#\" class=\"unidades\"><i class=\"fa fa-plus\" title=\"Cambiar unidades\"></i></a>";
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

    function initDataTable(autorizado) {
        var table = byId('datamaterial');
        if (!table) {
            return Promise.resolve();
        }

        return request('GET', '/api/material', {}, true).then(function (result) {
            var data = result && result.data ? result.data : [];
            var options = {
                data: data,
                deferRender: true,
                columns: [
                    { data: 'id' },
                    { data: 'descripcion' },
                    {
                        data: null,
                        render: function (row) {
                            return ESTADOS[row.estado];
                        }
                    },
                    { data: 'espacio' },
                    { data: 'unidades' },
                    { data: null, defaultContent: actionCellContent(autorizado) },
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
                new window.DataTable(table, options);
                return;
            }

            var jq = window.$;
            if (jq && jq.fn && typeof jq.fn.DataTable === 'function') {
                jq(table).DataTable(options);
            }
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
                { id: 'Estado', type: 'span', val: ESTADOS[result.data.estado] },
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

    function getDeleteInfo(anchor) {
        var row = anchor.closest('tr');
        var table = row ? row.closest('table') : null;
        var info = '\n';
        if (!row || !table) {
            return info;
        }
        var titles = table.querySelectorAll('thead th');
        Array.prototype.forEach.call(row.children, function (cell, i) {
            var html = trim(cell.innerHTML);
            if (html.length > 0) {
                var title = titles[i] ? trim(titles[i].textContent) : '';
                info += ' - ' + title + ': ' + cell.innerHTML + '\n';
            }
        });
        return info;
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

    function handleTableClick(event, autorizado) {
        var anchor = event.target.closest('a');
        if (!anchor) {
            return;
        }
        var row = anchor.closest('tr');
        if (!row) {
            return;
        }
        var idMaterial = getCellText(row, 0);
        if (!idMaterial) {
            return;
        }

        if (anchor.classList.contains('incidencia')) {
            anchor.setAttribute('href', '/material/' + idMaterial + '/incidencia');
            return;
        }

        if (anchor.classList.contains('ver')) {
            event.preventDefault();
            openViewModal(idMaterial);
            return;
        }

        if (!autorizado) {
            return;
        }

        if (anchor.classList.contains('delete')) {
            var info = getDeleteInfo(anchor);
            if (window.confirm('Vas a donar de baixa el lot:' + info)) {
                anchor.setAttribute('href', '/material/' + idMaterial + '/delete');
            } else {
                event.preventDefault();
            }
            return;
        }

        if (anchor.classList.contains('edit')) {
            anchor.setAttribute('href', '/material/' + idMaterial + '/edit');
            return;
        }

        if (anchor.classList.contains('copy')) {
            anchor.setAttribute('href', '/material/' + idMaterial + '/copy');
            return;
        }

        if (anchor.classList.contains('unidades')) {
            event.preventDefault();
            var unidadesCell = row.children[4];
            var unidades = parseInt(trim(unidadesCell ? unidadesCell.textContent : ''), 10);
            openEditModal({
                title: 'Cambiar unidades',
                controls: [
                    { id: 'Unidades', type: 'input', val: Number.isNaN(unidades) ? 0 : unidades },
                    { id: 'Explicacion', type: 'textarea' }
                ],
                onSubmit: function () {
                    var unidadesInput = byId('unidades');
                    var explicacionInput = byId('explicacion');
                    request('PUT', '/api/material/cambiarUnidad', {
                        id: idMaterial,
                        unidades: unidadesInput ? unidadesInput.value : '',
                        unidades_antes: Number.isNaN(unidades) ? 0 : unidades,
                        explicacion: explicacionInput ? explicacionInput.value : ''
                    }, false).then(function () {
                        if (unidadesCell && unidadesInput) {
                            unidadesCell.textContent = unidadesInput.value;
                        }
                    });
                }
            });
            return;
        }

        if (anchor.classList.contains('ubicacion')) {
            event.preventDefault();
            var espacioCell = row.children[3];
            var espacioTabla = trim(espacioCell ? espacioCell.textContent : '');
            openEditModal({
                title: 'Cambiar ubicación',
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
                    }, false).then(function () {
                        if (espacioCell && espacios) {
                            espacioCell.textContent = espacios.value;
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
            var estadoCell = row.children[2];
            var estadoMaterial = trim(estadoCell ? estadoCell.textContent : '');
            openEditModal({
                title: 'Cambiar estado',
                controls: [
                    { id: 'Estado', type: 'select' },
                    { id: 'Explicacion', type: 'textarea' }
                ],
                onSubmit: function () {
                    var estado = byId('estado');
                    var explicacion = byId('explicacion');
                    request('PUT', '/api/material/cambiarEstado', {
                        id: idMaterial,
                        estado: estado ? estado.value : '',
                        estado_antes: estadoMaterial,
                        explicacion: explicacion ? explicacion.value : ''
                    }, false).then(function () {
                        if (estadoCell && estado) {
                            estadoCell.textContent = estado.value;
                        }
                    });
                },
                afterRender: function () {
                    var selectEstado = byId('estado');
                    if (!selectEstado) {
                        return;
                    }
                    [
                        { value: '1', label: 'OK' },
                        { value: '2', label: 'Reparandose' },
                        { value: '3', label: 'Baja' }
                    ].forEach(function (item) {
                        var option = document.createElement('option');
                        option.value = item.value;
                        option.textContent = item.label;
                        if (item.value === estadoMaterial) {
                            option.selected = true;
                        }
                        selectEstado.appendChild(option);
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
        var autorizado = getAuthorized();
        initDataTable(autorizado);

        var table = byId('datamaterial');
        if (!table) {
            return;
        }

        table.addEventListener('click', function (event) {
            handleTableClick(event, autorizado);
        });

        table.addEventListener('change', function (event) {
            handleInventoryChange(event, autorizado);
        });
    });
})();
