'use strict';

var dias = ['L', 'M', 'X', 'J', 'V'];
var fecha = new Date('2017-10-17');

(function () {
    function byId(id) {
        return document.getElementById(id);
    }

    function trim(value) {
        return (value || '').toString().trim();
    }

    function getLegacyToken() {
        var tokenElement = byId('_token');
        return tokenElement ? trim(tokenElement.textContent) : '';
    }

    function getBearerToken() {
        var meta = document.querySelector('meta[name="user-bearer-token"]');
        return meta ? trim(meta.getAttribute('content')) : '';
    }

    function apiAuthOptions() {
        var options = { headers: {}, data: {} };
        var bearerToken = getBearerToken();
        var legacyToken = getLegacyToken();

        if (bearerToken) {
            options.headers.Authorization = 'Bearer ' + bearerToken;
        }
        if (legacyToken) {
            options.data.api_token = legacyToken;
        }

        return options;
    }

    function withQueryParams(url, params) {
        var query = new URLSearchParams(params || {}).toString();
        if (!query) {
            return url;
        }
        return url + (url.indexOf('?') === -1 ? '?' : '&') + query;
    }

    function fetchJson(url) {
        var auth = apiAuthOptions();
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

    function toggleNextWeekButton() {
        var next = document.querySelector('.next-week');
        if (!next) {
            return;
        }

        var fechaNextWeek = new Date(fecha);
        fechaNextWeek.setDate(fecha.getDate() + 7);
        next.style.display = fechaNextWeek > new Date() ? 'none' : '';
    }

    function updateHeaderDays() {
        var title = byId('profe-title');
        if (!title) {
            return;
        }

        var children = title.children;
        for (var i = 2; i < children.length; i += 1) {
            var idx = i - 2;
            if (idx < 5) {
                var queFecha = new Date(fecha);
                queFecha.setDate(queFecha.getDate() + idx);
                children[i].innerHTML = getFecha(queFecha);
            }
        }
    }

    function borraTabla() {
        document.querySelectorAll('#tabla-datos tbody td').forEach(function (cell) {
            cell.innerHTML = '';
        });
    }

    function cambiaFecha(deltaDias, borrar) {
        fecha.setDate(fecha.getDate() + deltaDias);
        toggleNextWeekButton();
        updateHeaderDays();

        if (borrar) {
            borraTabla();
            loadFichas();
        }
    }

    function loadFichas() {
        var fechaFin = new Date(fecha);
        fechaFin.setDate(fecha.getDate() + 4);
        var url = '/api/faltaProfesor/horas/dia]' + getFecha(fecha) + '&dia[' + getFecha(fechaFin);

        fetchJson(url)
            .then(function (res) {
                var message = res && res.message ? res.message : {};

                Object.keys(message).forEach(function (profe) {
                    var row = byId(profe);
                    if (!row) {
                        return;
                    }

                    var horasTot = 0;
                    var children = row.children;
                    for (var i = 2; i < children.length; i += 1) {
                        var idx = i - 2;
                        if (idx < 5) {
                            var queFecha = new Date(fecha);
                            queFecha.setDate(queFecha.getDate() + idx);
                            var key = getFecha(queFecha);

                            if (message[profe][key]) {
                                var horas = message[profe][key].horas;
                                children[i].innerHTML = horas;
                                horasTot += timeToSecs(horas);
                            }
                        } else {
                            children[i].innerHTML = '<b>' + secsToTime(horasTot) + '</b>';
                        }
                    }
                });
            })
            .catch(function (error) {
                window.console.error(error);
            });
    }

    document.addEventListener('DOMContentLoaded', function () {
        cambiaFecha(1 - fecha.getDay(), false);
        loadFichas();

        document.querySelectorAll('.next-week').forEach(function (button) {
            button.addEventListener('click', function (event) {
                event.preventDefault();
                cambiaFecha(7, true);
            });
        });

        document.querySelectorAll('.prev-week').forEach(function (button) {
            button.addEventListener('click', function (event) {
                event.preventDefault();
                cambiaFecha(-7, true);
            });
        });
    });
})();

function getFecha(fechaValue) {
    return fechaValue.toISOString().split('T')[0];
}

function secsToTime(secs) {
    var hours = parseInt(secs / (60 * 60), 10);
    secs -= hours * 60 * 60;
    var minutes = parseInt(secs / 60, 10);
    secs -= minutes * 60;
    return fillZero(hours) + ':' + fillZero(minutes) + ':' + fillZero(secs);
}

function timeToSecs(time) {
    var separatedTime = time.split(':');
    return separatedTime[0] * 60 * 60 + separatedTime[1] * 60 + Number(separatedTime[2]);
}

function fillZero(value, digits) {
    var totalDigits = digits || 2;
    var filled = '0000000000' + value;
    return filled.substr(filled.length - totalDigits);
}
