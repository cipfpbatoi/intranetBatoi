'use strict';

const MaxDiasAtras = 7;
const ocupacionGuardia = [
    { cod: 3249454, descrip: 'Sala de Profes' },
    { cod: 149034734, descrip: 'Biblioteca' }
];

var miIP = '';
var sesion = 0;
var guardiasSemana = [];
var idGuardia = 0;
var ipGuardia = [];
var biblio = false;
var codLugar = 0;
var diaHoy = '';
var diaSelec = '';
var horaActual = '';
var dias_semana = ['D', 'L', 'M', 'X', 'J', 'V', 'S'];

function trim(value) {
    return (value || '').toString().trim();
}

function getEl(id) {
    return document.getElementById(id);
}

function getDataToken() {
    var tokenNode = getEl('_token');
    return trim(tokenNode ? tokenNode.textContent : '');
}

function getBearerToken() {
    var bearerMeta = document.querySelector('meta[name="user-bearer-token"]');
    return trim(bearerMeta ? bearerMeta.getAttribute('content') : '');
}

function apiAuthOptions(extraData) {
    var legacyToken = getDataToken();
    var bearerToken = getBearerToken();
    var data = extraData || {};
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

function parseJsonSafe(response) {
    return response.text().then(function (text) {
        if (!text) {
            return {};
        }
        try {
            return JSON.parse(text);
        } catch (e) {
            return {};
        }
    });
}

function apiRequest(method, url, extraData) {
    var auth = apiAuthOptions(extraData);
    var options = {
        method: method,
        headers: Object.assign({}, auth.headers),
        credentials: 'same-origin'
    };

    if (method === 'GET') {
        url = withQueryParams(url, auth.data);
    } else {
        options.headers['Content-Type'] = 'application/x-www-form-urlencoded; charset=UTF-8';
        options.body = new URLSearchParams(auth.data).toString();
    }

    return fetch(url, options).then(function (response) {
        return parseJsonSafe(response).then(function (payload) {
            if (!response.ok) {
                var error = new Error('HTTP ' + response.status);
                error.status = response.status;
                error.statusText = response.statusText;
                error.payload = payload;
                throw error;
            }
            return payload;
        });
    });
}

function dateEspToISO(date) {
    var raw = trim(date);
    if (!raw) {
        return '';
    }

    if (/^\d{4}-\d{2}-\d{2}$/.test(raw)) {
        return raw;
    }

    var normalized = raw.replace(/\//g, '-');
    var arrFecha = normalized.split('-').map(function (dato) {
        return dato.length === 1 ? '0' + dato : dato;
    });

    if (arrFecha.length !== 3) {
        return '';
    }

    if (arrFecha[0].length === 4) {
        return arrFecha[0] + '-' + arrFecha[1] + '-' + arrFecha[2];
    }

    return arrFecha[2] + '-' + arrFecha[1] + '-' + arrFecha[0];
}

function showMessage(msg, tipo) {
    var container = document.querySelector('.x_content:last-of-type') || document.querySelector('.x_content');
    if (!container) {
        return;
    }

    var alertDiv = document.createElement('div');
    alertDiv.id = 'msg';
    alertDiv.className = 'alert ' + (tipo === 'error' ? 'alert-danger ' : 'alert-success ') + 'alert-block fade in';
    alertDiv.innerHTML = '<button type="button" class="close" data-dismiss="alert">x</button><p><strong>'
        + msg + '</strong></p>';
    container.appendChild(alertDiv);
}

function setControlEnabled(controlId, enabled) {
    var control = getEl(controlId);
    if (!control) {
        return;
    }
    control.disabled = !enabled;

    var formGroup = control.closest('.form-group');
    var label = formGroup ? formGroup.querySelector('label[for="' + controlId + '"]') : null;
    if (label) {
        label.classList.toggle('disabled', !enabled);
    }
}

function setSesion() {
    var horaSelect = getEl('hora');
    if (!horaSelect) {
        sesion = 0;
        return;
    }

    var targetOption = Array.from(horaSelect.options).find(function (elem) {
        var datosHora = (elem.textContent || '').split('-');
        return datosHora.length === 2 && horaActual >= datosHora[0] && horaActual <= datosHora[1];
    });

    sesion = targetOption ? targetOption.value : 0;
}

function getOptionSesion() {
    var horaSelect = getEl('hora');
    if (!horaSelect) {
        return null;
    }
    return Array.from(horaSelect.options).find(function (elem) {
        return String(elem.value) === String(sesion);
    }) || null;
}

function clearHoraOptions() {
    var horaSelect = getEl('hora');
    if (!horaSelect) {
        return;
    }
    Array.from(horaSelect.options).forEach(function (opt) {
        opt.disabled = true;
    });
    if (!horaSelect.querySelector('option[value="0"]')) {
        var option = document.createElement('option');
        option.value = '0';
        option.textContent = '-- Seleciona --';
        horaSelect.prepend(option);
    }
    horaSelect.value = '0';
}

function habilitaHoras(fecha) {
    var horaSelect = getEl('hora');
    var diaInput = getEl('dia');
    if (!horaSelect || !diaInput) {
        return;
    }

    clearHoraOptions();

    var horas = guardiasSemana.filter(function (guardia) {
        return guardia.dia_semana === dias_semana[fecha.getDay()];
    });
    var guardias_hoy = horas.length;

    horas.forEach(function (hora) {
        var option = horaSelect.querySelector('option[value="' + hora.sesion_orden + '"]');
        if (option) {
            option.disabled = false;
        }
    });

    if (!guardias_hoy) {
        showMessage('El día ' + diaInput.value + ' no tienes ninguna guardia', 'error');
        horaSelect.value = '0';
    } else {
        var optionSesion = getOptionSesion();
        if (optionSesion && !optionSesion.disabled) {
            horaSelect.value = String(sesion);
        }
    }

    cambiaHora();
}

function setPlace() {
    var infoIp = ipGuardia.find(function (item) {
        return item.ip === miIP;
    });
    if (infoIp) {
        biblio = true;
        codLugar = infoIp.codOcup;
        ocupacionGuardia.find(function (item) {
            return item.cod === infoIp.codOcup;
        });
    } else {
        codLugar = 1;
    }

    var legend = document.querySelector('legend.centrado');
    if (legend) {
        legend.textContent = 'Dades de la guardia';
    }

    cambiaHora();
}

function cambiaHora() {
    var horaSelect = getEl('hora');
    var diaInput = getEl('dia');
    var hecha = getEl('hecha');
    var obs = getEl('obs');
    var obsPer = getEl('obs_per');
    var dni = getEl('dni');

    if (!horaSelect || !diaInput || !hecha || !obs || !obsPer || !dni) {
        return;
    }

    hecha.checked = false;
    obs.value = '';
    obsPer.value = '';
    setControlEnabled('hecha', false);
    setControlEnabled('obs', false);

    var diaIso = dateEspToISO(diaInput.value);
    var correcto = false;

    if (codLugar && sesion && diaIso === diaHoy) {
        var dayIndex = new Date(diaIso).getDay();
        correcto = guardiasSemana.some(function (item) {
            return String(item.sesion_orden) === String(horaSelect.value)
                && item.dia_semana === dias_semana[dayIndex]
                && Number(item.ocupacion) === Number(codLugar);
        });
    }

    if (correcto) {
        setControlEnabled('hecha', true);
        setControlEnabled('obs', true);
        hecha.checked = true;
        obs.focus();
    } else {
        obsPer.focus();
    }

    if (parseInt(horaSelect.value, 10) > 0) {
        apiRequest('GET', 'api/guardia/idProfesor=' + trim(dni.textContent)
            + '&dia=' + diaIso
            + '&hora=' + horaSelect.value).then(function (res) {
            idGuardia = 0;
            var data = res.data || [];
            if (data.length) {
                var guardia = data[0];
                idGuardia = guardia.id;
                obs.value = guardia.observaciones || '';
                obsPer.value = guardia.obs_personal || '';
                hecha.checked = Number(guardia.realizada) === 1;
            }
        }, function (error) {
            showMessage('Error ' + (error.status || '') + ': ' + (error.statusText || 'Error'), 'error');
        });
    }
}

function modDatos(ev) {
    ev.preventDefault();

    var horaSelect = getEl('hora');
    var dni = getEl('dni');
    var diaInput = getEl('dia');
    var obs = getEl('obs');
    var obsPer = getEl('obs_per');
    var hecha = getEl('hecha');

    if (!horaSelect || !dni || !diaInput || !obs || !obsPer || !hecha) {
        return;
    }

    if (horaSelect.value === '0') {
        showMessage('Debes escoger la hora de la guardia', 'error');
        return;
    }

    var datosJson = {
        idProfesor: trim(dni.textContent),
        dia: dateEspToISO(diaInput.value),
        hora: horaSelect.value,
        observaciones: obs.value,
        obs_personal: obsPer.value,
        realizada: hecha.checked ? 1 : 0
    };

    var auth = apiAuthOptions();
    if (!auth.headers.Authorization && auth.data.api_token) {
        datosJson.api_token = auth.data.api_token;
    }

    var method = idGuardia ? 'PUT' : 'POST';
    var url = idGuardia ? '/api/guardia/' + idGuardia : '/api/guardia';

    apiRequest(method, url, datosJson).then(function () {
        showMessage('La guardia se ha guardado correctamente', 'ok');
    }, function (error) {
        showMessage('Error ' + (error.status || '') + ': ' + (error.statusText || 'Error'), 'error');
    });
}

function refreshSubmitState(nowDate) {
    var submit = getEl('submit');
    var diaInput = getEl('dia');
    if (!submit || !diaInput) {
        return;
    }

    var diaIso = dateEspToISO(diaInput.value);
    if (!diaIso) {
        submit.disabled = true;
        return;
    }
    var fechaSel = new Date(diaIso);

    if (fechaSel > nowDate) {
        submit.disabled = true;
        return;
    }

    var limit = new Date(fechaSel.getTime());
    limit.setDate(limit.getDate() + MaxDiasAtras);
    submit.disabled = (limit < nowDate);
}

function initDateValue(serverDate, serverTime) {
    var diaInput = getEl('dia');
    if (!diaInput) {
        return null;
    }

    diaHoy = serverDate;
    horaActual = serverTime;
    setSesion();

    var ahora = new Date(serverDate + 'T' + serverTime);
    diaInput.setAttribute('maxDate', diaHoy);
    diaInput.value = diaInput.type === 'date'
        ? serverDate
        : ahora.getDate() + '-' + (ahora.getMonth() + 1) + '-' + ahora.getFullYear();
    diaSelec = diaHoy;
    setSesion();
    return ahora;
}

function loadGuardiasSemana(dni) {
    return apiRequest('GET', '/api/horario/' + dni + '/guardia').then(function (res) {
        guardiasSemana = [];
        var data = res.data || [];
        for (var i in data) {
            if (Object.prototype.hasOwnProperty.call(data, i)) {
                guardiasSemana.push(data[i]);
            }
        }
    });
}

function initEvents(nowDate) {
    var diaInput = getEl('dia');
    var horaSelect = getEl('hora');
    var submit = getEl('submit');

    if (diaInput) {
        diaInput.addEventListener('change', function () {
            var diaIso = dateEspToISO(diaInput.value);
            if (!diaIso || diaIso === diaSelec) {
                return;
            }

            diaSelec = diaIso;
            var fechaSel = new Date(diaIso);
            habilitaHoras(fechaSel);
            refreshSubmitState(nowDate);
        });
    }

    if (horaSelect) {
        horaSelect.addEventListener('change', cambiaHora);
    }

    if (submit) {
        submit.addEventListener('click', modDatos);
    }
}

document.addEventListener('DOMContentLoaded', function () {
    var dniNode = getEl('dni');
    if (!dniNode) {
        return;
    }

    apiRequest('GET', '/api/server-time').then(function (serverTimeRes) {
        var nowDate = initDateValue(serverTimeRes.date, serverTimeRes.time);

        return Promise.all([
            apiRequest('GET', '/api/miIp').then(function (res) {
                miIP = res.data || '';
            }),
            apiRequest('GET', '/api/ipGuardias').then(function (res) {
                ipGuardia = res.data || [];
            }),
            loadGuardiasSemana(trim(dniNode.textContent))
        ]).then(function () {
            if (ipGuardia.length) {
                setPlace();
            }
            habilitaHoras(nowDate || new Date());
            refreshSubmitState(nowDate || new Date());
            initEvents(nowDate || new Date());
        });
    }, function (error) {
        showMessage('Error ' + (error.status || '') + ': ' + (error.statusText || 'Error'), 'error');
    });
});
