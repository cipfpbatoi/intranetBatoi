(function () {
    'use strict';

    var PRACTICAS = 31;
    var DUAL = 37;
    var ID = 'id';
    var TABLA = 'Empresa';

    function trim(value) {
        return (value || '').toString().trim();
    }

    function getApiAuth() {
        return window.intranetApiAuth || {};
    }

    function getById(id) {
        return document.getElementById(id);
    }

    function apiGet(url, extraData) {
        var apiAuth = getApiAuth();
        if (typeof apiAuth.apiGet === 'function') {
            return apiAuth.apiGet(url, extraData);
        }

        return Promise.reject(new Error('intranetApiAuth.apiGet no disponible'));
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

    function initDataTable(tableElement) {
        if (!tableElement) {
            return Promise.resolve(null);
        }

        var tableHelper = window.intranetDataTable || {};
        var hasV2 = typeof tableHelper.hasDataTableV2 === 'function' && tableHelper.hasDataTableV2();
        var hasJqDt = typeof tableHelper.hasJQueryDataTable === 'function' && tableHelper.hasJQueryDataTable();

        if (!hasV2 && !hasJqDt) {
            return Promise.resolve(null);
        }

        if (typeof tableHelper.isInitialized === 'function' && tableHelper.isInitialized(tableElement)) {
            return Promise.resolve(null);
        }

        tableElement.style.visibility = 'hidden';

        function withCommonOptions(options) {
            options.language = { url: '/json/cattable.json' };
            options.deferRender = true;
            options.autoWidth = false;
            options.responsive = true;
            options.columnDefs = options.columnDefs || [];
            options.columnDefs.push({
                responsivePriority: 1,
                targets: -1
            });
            return options;
        }

        function initWithOptions(options) {
            var originalInitComplete = options.initComplete;
            options.initComplete = function () {
                if (typeof originalInitComplete === 'function') {
                    originalInitComplete.apply(this, arguments);
                }
                if (dataTable && dataTable.columns && typeof dataTable.columns.adjust === 'function') {
                    dataTable.columns.adjust();
                }
                tableElement.style.visibility = 'visible';
            };

            var dataTable = typeof tableHelper.init === 'function'
                ? tableHelper.init(tableElement, options)
                : null;

            if (!dataTable) {
                tableElement.style.visibility = 'visible';
                return;
            }

            tableElement.addEventListener('draw.dt', function () {
                dataTable.columns.adjust();
            });
            tableElement.addEventListener('responsive-resize.dt', function () {
                dataTable.columns.adjust();
            });
            window.addEventListener('resize', function () {
                dataTable.columns.adjust();
            });
        }

        function initWithApiRows(rows) {
            initWithOptions(withCommonOptions({
                data: rows,
                rowId: ID,
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
                        targets: 8,
                        render: function (data, type, rowData) {
                            if (!rowData || !rowData.id) {
                                return '';
                            }
                            return '<a href="/empresa/' + rowData.id + '/detalle" class="shown"><i class="fa fa-plus" title="Mostrar"></i></a>';
                        }
                    }
                ]
            }));
        }

        function initFromDomFallback() {
            initWithOptions(withCommonOptions({}));
        }

        return apiGet('/api/convenio').then(function (result) {
            var rows = result && result.data ? result.data : [];
            initWithApiRows(rows);
        }).catch(function () {
            initFromDomFallback();
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
        var initPromise = initDataTable(tableElement);
        if (initPromise && typeof initPromise.catch === 'function') {
            initPromise.catch(function (error) {
                window.console.log(error);
            });
        }
    });
})();
