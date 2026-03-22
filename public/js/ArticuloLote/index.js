'use strict';

(function () {
    var options = {};
    var editar = "<a href=\"#\" class=\"edit\"><i class=\"fa fa-pencil\" title=\"Editar\"></i></a>";

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

    function trim(value) {
        return (value || '').toString().trim();
    }

    function withQueryParams(url, params) {
        var query = new URLSearchParams(params || {}).toString();
        if (!query) {
            return url;
        }
        return url + (url.indexOf('?') === -1 ? '?' : '&') + query;
    }

    function request(method, url, extraData, expectJson) {
        var auth = apiAuthOptions(extraData || {});
        var finalUrl = method === 'GET' ? withQueryParams(url, auth.data) : url;
        var optionsReq = {
            method: method,
            headers: Object.assign({}, auth.headers),
            credentials: 'same-origin'
        };

        if (method !== 'GET') {
            optionsReq.headers['Content-Type'] = 'application/x-www-form-urlencoded; charset=UTF-8';
            optionsReq.body = new URLSearchParams(auth.data).toString();
        }

        return fetch(finalUrl, optionsReq).then(function (response) {
            if (!response.ok) {
                throw new Error('HTTP ' + response.status);
            }
            if (expectJson === false) {
                return response.text();
            }
            return response.json();
        });
    }

    function getArticuloIdFromButton(button) {
        var row = button.closest('tr');
        if (!row) {
            return '';
        }
        var firstCell = row.querySelector('td');
        return trim(firstCell ? firstCell.textContent : '');
    }

    function loadEspacios() {
        return request('GET', '/api/espacio', {}, true).then(function (result) {
            options = {};
            (result.data || []).forEach(function (item) {
                options[item.aula] = item.descripcion;
            });
        });
    }

    function buildMaterialesTable(result) {
        var html = '<table id="datamateriales" name="material" class="table table-striped">';
        html += '<thead><tr><th>Id</th><th>Numero Serie</th><th>Marca</th><th>Modelo</th><th>Espai</th><th>Operacions</th></tr></thead><tbody>';
        var descripcion = '';

        (result.data || []).forEach(function (item) {
            html += '<tr id="' + item.id + '">';
            html += '<td>' + item.id + '</td>';
            html += '<td><span class="input" name="nserieprov">' + item.nserieprov + '</span></td>';
            html += '<td><span class="input" name="marca">' + item.marca + '</span></td>';
            html += '<td><span class="input" name="modelo">' + item.modelo + '</span></td>';
            html += '<td><span class="objselect" name="espacio">' + item.espacio + '</span></td>';
            html += '<td><span class="botones">' + editar + '</span></td>';
            html += '</tr>';
            descripcion = item.descripcion;
        });

        html += '</tbody></table>';
        return { html: html, descripcion: descripcion };
    }

    function cargaMateriales(entorno, idArticulo) {
        request('GET', '/api/articuloLote/' + idArticulo + '/materiales', {}, true).then(function (result) {
            var data = buildMaterialesTable(result);
            var dialogo = document.getElementById('dialogo');
            if (!dialogo) {
                return;
            }

            var title = dialogo.querySelector('.modal-title');
            var body = dialogo.querySelector('.modal-body');
            var submit = dialogo.querySelector('.modal-footer button[type=submit]');
            var close = dialogo.querySelector('.modal-footer button[type=button]');

            if (title) {
                title.innerHTML = "Materials del Article <span id='idLote'>" + data.descripcion + '</span>';
            }
            if (body) {
                body.innerHTML = data.html;
            }
            if (submit) {
                submit.style.display = 'none';
            }
            if (close) {
                close.textContent = 'Cerrar';
                close.onclick = function () {
                    hideModal('dialogo');
                };
            }

            showModal('dialogo');
        });
    }

    function onImageButtonClick(event) {
        var button = event.target.closest('a.imgButton');
        if (!button) {
            return;
        }
        event.preventDefault();
        var idArticulo = getArticuloIdFromButton(button);
        loadEspacios().then(function () {
            cargaMateriales(button, idArticulo);
        });
    }

    function onDialogEditClick(event) {
        var editButton = event.target.closest('#dialogo a.edit');
        if (!editButton) {
            return;
        }
        if (typeof window.editRow === 'function') {
            window.editRow.call(editButton, event);
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.addEventListener('click', onImageButtonClick);
        document.addEventListener('click', onDialogEditClick);
    });
})();
