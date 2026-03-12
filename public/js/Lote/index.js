'use strict';

const PROCEDENCIAS=['Desconocido', 'Dotación', 'Compra', 'Donación'];
const ESTADOS=[ 'BUIDA','ALTA', 'INVENTARIANT','FINALITZADA'];
const MANTENIMIENTO=7;
var selecionado = null;
var options = {};

function apiAuthOptions(extraData) {
    var tokenElement = document.getElementById('_token');
    var bearerMeta = document.querySelector('meta[name="user-bearer-token"]');
    var legacyToken = (tokenElement ? tokenElement.textContent : '').trim();
    var bearerToken = (bearerMeta ? bearerMeta.getAttribute('content') : '').trim();
    var data = extraData || {};
    var headers = {};

    if (bearerToken) {
        headers.Authorization = "Bearer " + bearerToken;
    }
    if (legacyToken) {
        data.api_token = legacyToken;
    }

    return { headers: headers, data: data };
}

function showModal(id) {
    if (window.intranetUiHelpers) {
        window.intranetUiHelpers.showModal(id);
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

function hideModal(id) {
    if (window.intranetUiHelpers) {
        window.intranetUiHelpers.hideModal(id);
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

    if (window.jQuery) {
        window.jQuery(modalElement).modal('hide');
    }
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
        if (data[key] !== undefined && data[key] !== null) {
            params.append(key, String(data[key]));
        }
    });
    return params.toString();
}

function requestJson(method, url, payload) {
    var auth = apiAuthOptions(payload || {});
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
        return response.json();
    });
}


    // FUncionalidades de los botones
    var rolElement = document.getElementById('rol');
    var rol = parseInt((rolElement ? rolElement.textContent : '').trim(), 10);
    var autorizado = !(rol % MANTENIMIENTO);
    var contenido = `<a href="#" class="ver">
                    <i class="fa fa-eye" title="Ver"></i>                
                </a>`;
    var borrar = `<a href="#" class="delete">
                    <i class="fa fa-trash" title="Borrar"></i>
                </a>`;
    var editar =  `<a href="#" class="edit">
                    <i class="fa fa-pencil" title="Editar"></i>
                </a>`;
    var operaciones = borrar + editar;
    var capturar = `<a href="#" class="capture">
                    <i class="fa fa-flag" title="Capturar des d'inventari"></i>
                </a>`;
    var inventariable = `<a href="#" class="inventary">
                    <i class="fa fa-cubes" title="Inventariar"></i>                
                </a>`;
    var auth = apiAuthOptions();
    var articulos = cargaArticulos();
    $("#datatable").DataTable( {
        ajax : {
            method: "GET",
            url: '/api/lote',
            headers: auth.headers,
            data: auth.data,
        },
        deferRender : true,
        dataSrc : 'data',
        rowId : 'registre',
        order: [[ 5, "desc" ]],
        columnDefs: [
            {className: "estado", "targets": [ 4 ]} ,
            {className: "operaciones", "targets": [ 7 ]} ,
        ],
        columns: [
            { data:'registre'},
            { data:'proveedor'},
            { data:'factura'},
            {
                data: null, render: function (data) {
                    if (data.procedencia)
                        return PROCEDENCIAS[data.procedencia];
                    else return 'Desconocido';
                    },
            },
            {
                data: null, render: function (data) {
                        return ESTADOS[data.estado];
                    },
            },
            { data:'fechaAlta'},
            { data:'departamento'},
            { data: null, render: function (data){
                return  (data.estado == 1) ? contenido+operaciones+inventariable:
                        (data.estado == 2) ? contenido+editar:
                        (data.estado == 0) ? contenido+operaciones+capturar:contenido+editar+`<a href="/direccion/lote/`+data.registre+`/print" class="QR">
                                <i class="fa fa-barcode" title="Codi QR"></i>                
                            </a>`;
                },
            },
        ],
//        rowCallback: function ( row, data ) {
//            // Set the checked state of the checkbox in the table
//            $('input.editor-active', row).prop( 'checked', data.fechaultimoinventario != null );
//        },
        language: {
            url: "/json/cattable.json"
        }
    });

document.addEventListener('DOMContentLoaded', function () {
    var table = document.getElementById('datatable');
    var dialogo = document.getElementById('dialogo');
    var materiales = document.getElementById('materiales');

    document.querySelectorAll('.txtButton').forEach(function (button) {
        button.addEventListener('click', function (event) {
            event.preventDefault();
            showModal('create');
        });
    });

    if (table) {
        table.addEventListener('click', function (event) {
            var link = event.target.closest('a');
            if (!link) {
                return;
            }
            var row = link.closest('tr');
            if (!row) {
                return;
            }
            var cells = row.children;
            var idLote = (cells[0] ? cells[0].textContent : '').trim();

            if (link.classList.contains('ver')) {
                event.preventDefault();
                var estadoText = (row.querySelector('.estado') ? row.querySelector('.estado').textContent : '').trim();
                var estado = (estadoText === 'ALTA' || estadoText === 'BUIDA');
                cargaArticulosLote(link, idLote, estado);
                return;
            }

            if (!autorizado) {
                return;
            }

            if (link.classList.contains('delete')) {
                var info = '\n';
                var titles = table.querySelectorAll('thead th');
                Array.prototype.forEach.call(cells, function (item, i) {
                    if (item.innerHTML.trim().length > 0) {
                        info += ' - ' + (titles[i] ? titles[i].textContent.trim() : '') + ': ' + item.innerHTML + '\n';
                    }
                });
                if (confirm('Vas a borrar el elemento:' + info)) {
                    requestJson('DELETE', '/api/lote/' + idLote, {}).then(function (result) {
                        if (result.success === true) {
                            row.classList.add('danger');
                        }
                    });
                } else {
                    event.preventDefault();
                }
                return;
            }

            if (link.classList.contains('capture')) {
                window.location.href = '/direccion/lote/' + idLote + '/capture';
                return;
            }

            if (link.classList.contains('edit')) {
                event.preventDefault();
                var proveedor = (cells[1] ? cells[1].textContent : '').trim();
                var factura = (cells[2] ? cells[2].textContent : '').trim();
                var origen = (cells[3] ? cells[3].textContent : '').trim();
                var fechaAlta = (cells[5] ? cells[5].textContent : '').trim();

                var dlgControls = [
                    { id: 'Registre', type: 'text', val: idLote },
                    { id: 'Factura', type: 'text', val: factura },
                    { id: 'Proveedor', type: 'text', val: proveedor },
                    { id: 'Origen', type: 'select' },
                    { id: 'FechaAlta', type: 'text', val: fechaAlta }
                ];

                var title = dialogo ? dialogo.querySelector('.modal-title') : null;
                var body = dialogo ? dialogo.querySelector('.modal-body') : null;
                var closeBtn = dialogo ? dialogo.querySelector('.modal-footer button[type=button]') : null;
                var submitBtn = dialogo ? dialogo.querySelector('.modal-footer button[type=submit]') : null;

                if (title) title.textContent = 'Editar Factura ' + idLote;
                if (body) body.innerHTML = htmlDialog(dlgControls);
                if (closeBtn) closeBtn.textContent = 'Cancelar';
                if (submitBtn) {
                    submitBtn.style.display = '';
                    submitBtn.onclick = function () {
                        requestJson('PUT', '/api/lote/' + idLote, {
                            registre: document.getElementById('registre').value,
                            factura: document.getElementById('factura').value,
                            proveedor: document.getElementById('proveedor').value,
                            procedencia: document.getElementById('origen').value,
                            fechaAlta: document.getElementById('fechaalta').value
                        }).then(function () {
                            if (cells[0]) cells[0].textContent = document.getElementById('registre').value;
                            if (cells[1]) cells[1].textContent = document.getElementById('proveedor').value;
                            if (cells[2]) cells[2].textContent = document.getElementById('factura').value;
                            if (cells[3]) cells[3].textContent = PROCEDENCIAS[document.getElementById('origen').value];
                            if (cells[5]) cells[5].textContent = document.getElementById('fechaalta').value;
                        }).catch(function (error) {
                            console.log(error);
                        });
                        hideModal('dialogo');
                    };
                }

                var origenSelect = document.getElementById('origen');
                if (origenSelect) {
                    origenSelect.innerHTML = '';
                    [
                        { v: '0', t: 'Desconocido' },
                        { v: '1', t: 'Dotación' },
                        { v: '2', t: 'Compra' },
                        { v: '3', t: 'Donación' }
                    ].forEach(function (o) {
                        var option = document.createElement('option');
                        option.value = o.v;
                        option.textContent = o.t;
                        if (o.t === origen) option.selected = true;
                        origenSelect.appendChild(option);
                    });
                }
                showModal('dialogo');
                return;
            }

            if (link.classList.contains('inventary')) {
                var texto = 'Vas a inventariar el lot amb registre ' + idLote + ', amb els següents articles i avisar al cap de departament.\n';
                requestJson('GET', '/api/lote/' + idLote + '/articulos', {}).then(function (result) {
                    (result.data || []).forEach(function (item) {
                        texto += item.unidades + ' de ' + item.descripcion + '\n';
                    });
                    if (confirm(texto)) {
                        requestJson('PUT', '/api/lote/' + idLote + '/articulos', { inventariar: true }).then(function (r) {
                            if (r.success === true) {
                                row.classList.add('danger');
                                var estadoNode = row.querySelector('.estado');
                                if (estadoNode) estadoNode.textContent = 'INVENTARIANT';
                                if (cells[7]) cells[7].innerHTML = contenido;
                            }
                        }).catch(function (error) {
                            console.log(error);
                        });
                    }
                });
            }
        });
    }

    if (dialogo) {
        dialogo.addEventListener('change', function (event) {
            var articuloSelect = event.target.closest('#articulo_id');
            if (!articuloSelect) {
                return;
            }
            var descripcion = document.getElementById('descripcion');
            if (!descripcion) return;
            if (articuloSelect.value === 'new') descripcion.classList.remove('hidden');
            else descripcion.classList.add('hidden');
        });

        dialogo.addEventListener('click', function (event) {
            var link = event.target.closest('a');
            if (!link) return;

            if (link.classList.contains('ver')) {
                event.preventDefault();
                hideModal('dialogo');
                var row = link.closest('tr');
                var idArticulo = row && row.children[0] ? row.children[0].textContent.trim() : '';
                requestJson('GET', '/api/espacio', {}).then(function (result) {
                    (result.data || []).forEach(function (item) {
                        options[item.aula] = item.descripcion;
                    });
                    cargaMateriales(link, idArticulo);
                });
                return;
            }

            if (link.classList.contains('new')) {
                event.preventDefault();
                requestJson('POST', '/api/articuloLote', {
                    lote_id: (document.getElementById('idLote') || {}).textContent || '',
                    articulo_id: (document.getElementById('articulo_id') || {}).value || '',
                    descripcion: (document.getElementById('descripcion') || {}).value || '',
                    marca: (document.getElementById('marca') || {}).value || '',
                    modelo: (document.getElementById('modelo') || {}).value || '',
                    unidades: (document.getElementById('unidades') || {}).value || ''
                }).then(function (result) {
                    if (result.success === true) {
                        cargaArticulosLote(link, (document.getElementById('idLote') || {}).textContent || '', true);
                    }
                }).catch(function (error) {
                    console.log(error);
                });
                return;
            }

            if (link.classList.contains('delete')) {
                var rowDel = link.closest('tr');
                var infoDel = '\n';
                var tableDel = rowDel ? rowDel.closest('table') : null;
                var titlesDel = tableDel ? tableDel.querySelectorAll('thead th') : [];
                if (rowDel) {
                    Array.prototype.forEach.call(rowDel.children, function (item, i) {
                        if (item.innerHTML.trim().length > 0) {
                            infoDel += ' - ' + (titlesDel[i] ? titlesDel[i].textContent.trim() : '') + ': ' + item.innerHTML + '\n';
                        }
                    });
                }
                if (confirm('Vas a borrar el elemento:' + infoDel)) {
                    var idDelete = rowDel && rowDel.children[0] ? rowDel.children[0].textContent.trim() : '';
                    requestJson('DELETE', '/api/articuloLote/' + idDelete, {}).then(function (result) {
                        if (result.success === true) {
                            cargaArticulosLote(link, (document.getElementById('idLote') || {}).textContent || '', true);
                        }
                    });
                } else {
                    event.preventDefault();
                }
                return;
            }

            if (link.classList.contains('edit') && typeof editRow === 'function') {
                editRow.call(link, event);
            }
        });
    }

    if (materiales) {
        materiales.addEventListener('click', function (event) {
            var link = event.target.closest('a.edit');
            if (link && typeof editRow === 'function') {
                editRow.call(link, event);
            }
        });
    }
});

function cargaArticulosLote(entorno,idLote,estado){
    requestJson("GET", "/api/lote/" + idLote+"/articulos", {}).then(function (result) {
        var html = '<table id="dataarticle" name="articuloLote" class="table table-striped"><thead><tr><th>Id</th><th>Article</th><th>Marca</th><th>Mòdel</th><th>Unitats</th>';
        html += '<th>Operacions</th></tr></thead><tbody>';
        (result.data || []).forEach(function (item) {
            html += '<tr id="'+item.id+'"><td>'+item.id+'</span></td><td><span class="none" name="descripcion">'+item.descripcion+
                '</span></td><td><span class="input" name="marca">'+ item.marca+
                '</span></td><td><span class="input" name="modelo">'+item.modelo+
                '</td><td class="unidades"><span class="input" name="unidades">'+item.unidades+'</span></td><td><span class="botones">'+contenido;
            if (estado) html += operaciones;
        });
        if (estado) {
            html += '</span></td></tr><tr><td></td><td>' + articulos +
                '<br/><input type="text" id="descripcion" name="descripcion" class="hidden"></td>'+
                '<td><input type="text" id="marca" name="marca" /></td>'+
                '<td><input type="text" id="modelo" name="modelo" /></td>'+
                '<td><input type=number" id="unidades" name="unidades" /></td><td><a class="btn btn-info new">Afegir Article</a></td></tr>';
        }
        html += '</tbody></table>';
        var dialogo = document.getElementById('dialogo');
        if (!dialogo) {
            return;
        }
        var title = dialogo.querySelector('.modal-title');
        var body = dialogo.querySelector('.modal-body');
        var submit = dialogo.querySelector('.modal-footer button[type=submit]');
        var close = dialogo.querySelector('.modal-footer button[type=button]');
        if (title) title.innerHTML = "Articles del Lot <span id='idLote'>"+result.lote+'</span>';
        if (body) body.innerHTML = html;
        if (submit) submit.style.display = 'none';
        if (close) close.textContent = 'Cerrar';
        showModal('dialogo');
    }).catch(function (error) {
        console.log(error);
    });
}

function cargaArticulos() {
    requestJson("GET", "/api/articulo", {}).then(function (result) {
        articulos = '<select id="articulo_id" name="articulo_id" ><option value="new">Article Nou</option>';
        (result.data || []).forEach(function (item, i) {
            if (i == 0) {
                articulos += "<option value='" + item.id + "' selected>" + item.descripcion + "</option>";
            } else {
                articulos += "<option value='" + item.id + "'>" + item.descripcion + "</option>";
            }
        });
    }).catch(function (error) {
        console.log(error);
    });
}

function cargaMateriales(entorno,idArticulo){
    requestJson("GET", "/api/articuloLote/" + idArticulo +"/materiales", {}).then(function (result) {
        var html = '<table id="datamateriales" name="material" class="table table-striped"><thead><tr><th>Id</th><th>Numero Serie</th><th>Espai</th>';
        var descripcion = '';
        html += '<th>Operacions</th></tr></thead><tbody>';
        (result.data || []).forEach(function (item) {
            html += '<tr id="'+item.id+'"><td>'+item.id+'</span></td>'+
                '<td><span class="input" name="nserieprov">'+ item.nserieprov+
                '</span></td><td><span class="objselect" name="espacio">'+item.espacio+
                '</td><td><span class="botones">'+editar;
            descripcion = item.descripcion;
        });
        html += '</tbody></table>';
        var materiales = document.getElementById('materiales');
        if (!materiales) {
            return;
        }
        var title = materiales.querySelector('.modal-title');
        var body = materiales.querySelector('.modal-body');
        var submit = materiales.querySelector('.modal-footer button[type=submit]');
        var close = materiales.querySelector('.modal-footer button[type=button]');
        if (title) title.innerHTML = "Materials del Article <span id='idLote'>"+descripcion+'</span>';
        if (body) body.innerHTML = html;
        if (submit) submit.style.display = 'none';
        if (close) {
            close.textContent = 'Cerrar';
            close.onclick = function () {
                showModal('dialogo');
                hideModal('materiales');
            };
        }
        showModal('materiales');
    }).catch(function (error) {
        console.log(error);
    });
}


function htmlDialog(dlgControls) {
    let html=`<form class="form-horizontal form-label-left">
        `;
    for (let control of dlgControls) {
        html+=`<div class="form-group">
            `;
        html+=`<label class="control-label col-md-3 col-sm-3 col-xs-12" for="${control.id.toLowerCase()}">${control.id}</label>
                `;
        html+=`<div class="col-md-6 col-sm-6 col-xs-12">`;
        switch (control.type) {
            case 'text':
                html+=`<input type="text" id="${control.id.toLowerCase()}" `;
                if (control.val!=undefined)
                    html+=`value="${control.val}" `;
                html+=`class="form-control" />
                    `;
                break;
            case 'input':
                html+=`<input type="number" id="${control.id.toLowerCase()}" `;
                if (control.val!=undefined)
                    html+=`value="${control.val}" `;
                html+=`class="form-control" />
                    `;
                break;
            case 'select':
                html+=`<select id="${control.id.toLowerCase()}" class="form-control"></select>
                    `;
                break;
            case 'textarea':
                html+=`<textarea id="${control.id.toLowerCase()}" class="form-control"></textarea>
                    `;
                break;
            case 'span':
                html+=`<span id="${control.id.toLowerCase()}" class="form-control">`;
                if (control.val!=undefined)
                    html+=control.val;
                html+=`</span>
                    `;
                break;
        }
        html+=`</div>
        </div>
        `;
    }
    html+=`</form>`;
    return html;
}
