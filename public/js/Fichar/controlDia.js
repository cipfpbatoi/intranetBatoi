'use strict';

var dias = ['Lun', 'Mar', 'Mie', 'Jue', 'Vie'];
var fecha = new Date();

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

    function updateTitleDate() {
        var title = byId('profe-title');
        if (!title || !title.lastElementChild) {
            return;
        }
        title.lastElementChild.textContent = dias[fecha.getDay() - 1] + ' ' + getFecha(fecha);
    }

    function borraTabla() {
        document.querySelectorAll('#tabla-datos tbody td > span.fichaje').forEach(function (span) {
            span.innerHTML = '';
        });
    }

    function cambiaFecha(deltaDias, borrar) {
        fecha.setDate(fecha.getDate() + deltaDias);
        if (fecha.getDay() === 0) {
            fecha.setDate(fecha.getDate() - 2);
        }
        if (fecha.getDay() === 6) {
            fecha.setDate(fecha.getDate() + 2);
        }

        var nextDay = document.querySelector('.next-day');
        if (nextDay) {
            nextDay.style.display = getFecha(fecha) >= getFecha(new Date()) ? 'none' : '';
        }

        if (borrar) {
            borraTabla();
            loadFichas();
            loadHorario();
        }
    }

    function loadFichas() {
        var url = '/api/faltaProfesor/dia=' + getFecha(fecha);

        fetchJson(url)
            .then(function (res) {
                var data = res && res.data ? res.data : [];
                data.forEach(function (ficha) {
                    var row = byId(ficha.idProfesor);
                    if (!row || !row.children[3]) {
                        return;
                    }
                    var target = row.children[3].querySelector('.fichaje');
                    if (target) {
                        target.innerHTML += ficha.entrada + '->' + ficha.salida + '<br>';
                    }
                });
                updateTitleDate();
            })
            .catch(function (error) {
                window.console.error(error);
            });
    }

    function loadHorario() {
        var url = '/api/horariosDia/' + getFechaEsp(fecha);

        fetchJson(url)
            .then(function (res) {
                var data = res && res.data ? res.data : {};
                Object.keys(data).forEach(function (idProfesor) {
                    var row = byId(idProfesor);
                    if (!row || !row.children[2]) {
                        return;
                    }
                    var target = row.children[2].querySelector('.fichaje');
                    if (target) {
                        target.innerHTML += data[idProfesor];
                    }
                });
                updateTitleDate();
            })
            .catch(function (error) {
                window.console.error(error);
            });
    }

    document.addEventListener('DOMContentLoaded', function () {
        loadFichas();
        loadHorario();

        document.querySelectorAll('.next-day').forEach(function (button) {
            button.style.display = 'none';
            button.addEventListener('click', function (event) {
                event.preventDefault();
                cambiaFecha(1, true);
            });
        });

        document.querySelectorAll('.prev-day').forEach(function (button) {
            button.addEventListener('click', function (event) {
                event.preventDefault();
                cambiaFecha(-1, true);
            });
        });
    });
})();

function getFecha(fechaValue) {
    return fechaValue.toISOString().split('T')[0];
}

function getFechaEsp(fechaValue) {
    var fechaInt = getFecha(fechaValue).split('-');
    return fechaInt[2] + '-' + fechaInt[1] + '-' + fechaInt[0];
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
