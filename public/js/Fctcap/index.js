'use strict';

(function () {
    function trim(value) {
        return (value || '').toString().trim();
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
        if (!bearerToken && legacyToken) {
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

    function updateAlumnoFct(idFct, payload) {
        var auth = apiAuthOptions(payload);
        var headers = Object.assign({}, auth.headers, {
            'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8'
        });

        return fetch('/api/alumnofct/' + idFct, {
            method: 'PUT',
            headers: headers,
            body: toFormBody(auth.data),
            credentials: 'same-origin'
        }).then(function (response) {
            if (!response.ok) {
                throw new Error('HTTP ' + response.status);
            }
            return response.text();
        });
    }

    function getRowIdFromCheckbox(checkbox) {
        var row = checkbox ? checkbox.closest('tr') : null;
        var firstCell = row ? row.querySelector('td') : null;
        return firstCell ? trim(firstCell.textContent) : '';
    }

    function onEditorChange(event) {
        var checkbox = event.target;
        if (!checkbox.classList.contains('editor-active')) {
            return;
        }

        var idFct = getRowIdFromCheckbox(checkbox);
        if (!idFct) {
            return;
        }

        var checked = checkbox.checked;
        var previous = !checked;
        var payload = { id: idFct };
        payload[checkbox.classList.contains('a56') ? 'a56' : 'pg0301'] = checked;

        updateAlumnoFct(idFct, payload).catch(function (error) {
            checkbox.checked = previous;
            window.console.log(error);
        });
    }

    function getTableOptions(data, avise) {
        return {
            data: data || [],
            deferRender: true,
            columns: [
                { data: 'id' },
                { data: 'nombre' },
                { data: 'centro' },
                { data: 'desde' },
                { data: 'hasta' },
                {
                    data: null,
                    render: function (row) {
                        var ret = ' <a href="/fct/' + row.id + '/link" class="imgButton"><i class="fa fa-paperclip"></i></a> ' + avise;
                        if (row.a56 === 1) {
                            ret += ' <a href="/fct/' + row.id + '/sendAnexo" class="imgButton"><i class="fa fa-plane"></i></a> ';
                        }
                        return ret;
                    }
                },
                {
                    data: null,
                    render: function (row) {
                        return row.pg0301
                            ? '<input type="checkbox" checked class="editor-active">'
                            : '<input type="checkbox" class="editor-active"> ';
                    }
                },
                {
                    data: null,
                    render: function (row) {
                        if (row.a56) {
                            return row.pg0301
                                ? ' <input type="checkbox" checked class="editor-active a56">'
                                : ' <input type="checkbox" disabled checked class="editor-active a56">';
                        }
                        return row.pg0301
                            ? ' <input type="checkbox" class="editor-active a56">'
                            : ' <input type="checkbox" disabled class="editor-active a56">';
                    }
                }
            ],
            language: {
                url: '/json/cattable.json'
            }
        };
    }

    function initDataTable(tableElement, options) {
        if (!tableElement) {
            return;
        }

        if (typeof window.DataTable === 'function') {
            new window.DataTable(tableElement, options);
            return;
        }

        var jq = window.$;
        if (jq && jq.fn && typeof jq.fn.DataTable === 'function') {
            jq(tableElement).DataTable(options);
        }
    }

    document.addEventListener('DOMContentLoaded', function () {
        var groupElement = document.getElementById('_grupo');
        var grupo = trim(groupElement ? groupElement.textContent : '');
        var avise = ' <a href="/profesor/mensaje" class="mensaje"><i class="fa fa-bell"></i></a> ';
        var tableElement = document.getElementById('dataFct');
        if (grupo && tableElement) {
            var authDatatable = apiAuthOptions();
            fetchJson('/api/alumnofct/' + grupo + '/grupo', authDatatable)
                .then(function (result) {
                    var options = getTableOptions((result && result.data) || [], avise);
                    initDataTable(tableElement, options);
                })
                .catch(function (error) {
                    window.console.log(error);
                });
        }

        if (tableElement) {
            tableElement.addEventListener('change', onEditorChange);
        }
    });
})();
