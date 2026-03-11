(function () {
    'use strict';

    var PRACTICAS = 31;
    var DUAL = 37;
    var ID = 'id';
    var TABLA = 'Empresa';

    function trim(value) {
        return (value || '').toString().trim();
    }

    function getById(id) {
        return document.getElementById(id);
    }

    function apiAuthOptions(extraData) {
        var tokenNode = getById('_token');
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

    function withQueryParams(url, params) {
        var query = new URLSearchParams(params || {}).toString();
        if (!query) {
            return url;
        }
        return url + (url.indexOf('?') === -1 ? '?' : '&') + query;
    }

    function fetchJson(url, auth) {
        return fetch(withQueryParams(url, auth.data), {
            method: 'GET',
            headers: auth.headers,
            credentials: 'same-origin'
        }).then(function (response) {
            if (!response.ok) {
                throw new Error('HTTP ' + response.status);
            }
            return response.json();
        });
    }

    function getAutorizado() {
        var rolNode = getById('rol');
        var rol = parseInt(trim(rolNode ? rolNode.textContent : ''), 10);
        if (Number.isNaN(rol)) {
            return false;
        }
        return (rol % PRACTICAS === 0) || (rol % DUAL === 0);
    }

    function getRowInfo(source) {
        var row = source ? source.closest('tr') : null;
        var table = source ? source.closest('table') : null;
        if (!row || !table) {
            return '\n';
        }

        var headers = table.querySelectorAll('thead th');
        var cells = row.querySelectorAll('td');
        var info = '\n';

        for (var i = 0; i < cells.length; i += 1) {
            var cell = cells[i];
            var text = trim(cell ? cell.textContent : '');
            if (!text) {
                continue;
            }

            var header = headers[i] ? trim(headers[i].textContent) : '';
            info += ' - ' + header + ': ' + cell.innerHTML + '\n';
        }

        return info;
    }

    function initDataTable(tableElement, autorizado) {
        if (!tableElement || typeof window.DataTable !== 'function') {
            return null;
        }

        if (typeof window.DataTable.isDataTable === 'function' && window.DataTable.isDataTable(tableElement)) {
            return null;
        }

        tableElement.style.visibility = 'hidden';
        var authDatatable = apiAuthOptions();

        return fetchJson('/api/convenio', authDatatable).then(function (result) {
            var rows = result && result.data ? result.data : [];
            var dataTable = new window.DataTable(tableElement, {
                language: {
                    url: '/json/cattable.json'
                },
                data: rows,
                deferRender: true,
                autoWidth: false,
                rowId: ID,
                responsive: true,
                columns: [
                    { data: 'concierto' },
                    { data: 'nombre' },
                    { data: 'direccion' },
                    { data: 'localidad' },
                    { data: 'telefono' },
                    { data: 'email' },
                    { data: 'cif' },
                    { data: 'actividad' },
                    { data: null }
                ],
                createdRow: function (row, rowData) {
                    if (rowData && rowData.conveni) {
                        row.classList.add('bg-green');
                    }
                },
                columnDefs: [
                    {
                        responsivePriority: 1,
                        targets: 8,
                        render: function (data, type, rowData) {
                            if (!autorizado || !rowData || !rowData.id) {
                                return '';
                            }
                            return '<a href="/empresa/' + rowData.id + '/detalle" class="shown"><i class="fa fa-plus" title="Mostrar"></i></a>';
                        }
                    }
                ],
                initComplete: function () {
                    if (dataTable && dataTable.columns && typeof dataTable.columns.adjust === 'function') {
                        dataTable.columns.adjust();
                    }
                    tableElement.style.visibility = 'visible';
                }
            });

            tableElement.addEventListener('draw.dt', function () {
                dataTable.columns.adjust();
            });
            tableElement.addEventListener('responsive-resize.dt', function () {
                dataTable.columns.adjust();
            });
            window.addEventListener('resize', function () {
                dataTable.columns.adjust();
            });
        });
    }

    function bindTableActions(tableElement) {
        if (!tableElement) {
            return;
        }

        tableElement.addEventListener('click', function (event) {
            var deleteLink = event.target.closest('a.delete');
            if (deleteLink) {
                var info = getRowInfo(deleteLink);
                if (confirm('Vas a borrar el elemento:' + info)) {
                    var row = deleteLink.closest('tr');
                    var rowId = row ? row.id : '';
                    deleteLink.setAttribute('href', '/' + TABLA.toLowerCase() + '/' + rowId + '/delete');
                } else {
                    event.preventDefault();
                }
                return;
            }

            var documentLink = event.target.closest('a.document');
            if (documentLink) {
                var rowDoc = documentLink.closest('tr');
                var rowDocId = rowDoc ? rowDoc.id : '';
                documentLink.setAttribute('href', '/' + TABLA.toLowerCase() + '/' + rowDocId + '/document');
            }
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        var tableElement = getById('datatable');
        if (!tableElement) {
            return;
        }

        bindTableActions(tableElement);
        initDataTable(tableElement, getAutorizado()).catch(function (error) {
            window.console.log(error);
        });
    });
})();
