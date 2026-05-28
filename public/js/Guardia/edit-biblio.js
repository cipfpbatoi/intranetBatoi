'use strict';

const MaxDiasAtras = 7;
const ocupacionGuardia = 3249454;

var sesion = 0;
var guardiasSemana = [];
var idGuardia = 0;
var ipGuardia = [];
var biblio = false;
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
    var arrFecha = (date || '').split('-');
    arrFecha = arrFecha.map(function (dato) {
        return dato.length === 1 ? '0' + dato : dato;
    });
    if (arrFecha.length !== 3) {
        return '';
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
    var guardiasHoy = horas.length;

    horas.forEach(function (hora) {
        var option = horaSelect.querySelector('option[value="' + hora.sesion_orden + '"]');
        if (option) {
            option.disabled = false;
        }
    });

    if (!guardiasHoy) {
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
    if (biblio && sesion && String(horaSelect.value) === String(sesion) && diaIso === diaHoy) {
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
                hecha.checked = Boolean(guardia.realizada);
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

function collectLocalIps() {
    return new Promise(function (resolve) {
        var ips = [];
        var dedup = {};
        var RTCPeerConnection = window.RTCPeerConnection
            || window.mozRTCPeerConnection
            || window.webkitRTCPeerConnection;

        if (!RTCPeerConnection) {
            resolve([]);
            return;
        }

        var pc = new RTCPeerConnection({ iceServers: [{ urls: 'stun:stun.services.mozilla.com' }] });
        pc.onicecandidate = function (ice) {
            if (!ice || !ice.candidate || !ice.candidate.candidate) {
                return;
            }
            var match = /([0-9]{1,3}(\.[0-9]{1,3}){3})/.exec(ice.candidate.candidate);
            if (!match) {
                return;
            }
            var ip = match[1];
            if (!dedup[ip]) {
                dedup[ip] = true;
                ips.push(ip);
            }
        };

        try {
            pc.createDataChannel('');
            pc.createOffer().then(function (result) {
                return pc.setLocalDescription(result);
            }).catch(function () {
                resolve([]);
            });
        } catch (e) {
            resolve([]);
            return;
        }

        setTimeout(function () {
            try {
                pc.close();
            } catch (e) {
                // ignore
            }
            resolve(ips);
        }, 1000);
    });
}

document.addEventListener('DOMContentLoaded', function () {
    var horaSelect = getEl('hora');
    var diaInput = getEl('dia');
    var dni = getEl('dni');
    var submit = getEl('submit');
    if (!horaSelect || !diaInput || !dni || !submit) {
        return;
    }

    clearHoraOptions();

    var ahora = new Date();
    diaHoy = ahora.toISOString().split('T')[0];
    horaActual = ahora.toTimeString().substr(0, 5);
    setSesion();
    diaInput.setAttribute('maxDate', diaHoy);
    diaInput.value = ahora.getDate() + '-' + (ahora.getMonth() + 1) + '-' + ahora.getFullYear();
    diaSelec = diaHoy;

    Promise.all([
        apiRequest('GET', '/api/ipGuardias').then(function (res) {
            ipGuardia = res.data || [];
        }),
        collectLocalIps(),
        apiRequest('GET', '/api/horario/idProfesor=' + trim(dni.textContent) + '&ocupacion=' + ocupacionGuardia).then(function (res) {
            guardiasSemana = [];
            var data = res.data || [];
            for (var i in data) {
                if (Object.prototype.hasOwnProperty.call(data, i)) {
                    guardiasSemana.push(data[i]);
                }
            }
        })
    ]).then(function (resultados) {
        var localIps = resultados[1] || [];
        biblio = localIps.some(function (ip) {
            return ipGuardia.indexOf(ip) !== -1;
        });

        habilitaHoras(ahora);
        refreshSubmitState(ahora);

        diaInput.addEventListener('change', function () {
            var diaIso = dateEspToISO(diaInput.value);
            if (!diaIso || diaIso === diaSelec) {
                return;
            }

            diaSelec = diaIso;
            var fechaSel = new Date(diaIso);
            habilitaHoras(fechaSel);
            refreshSubmitState(ahora);
        });

        horaSelect.addEventListener('change', cambiaHora);
        submit.addEventListener('click', modDatos);
    }, function (error) {
        showMessage('Error ' + (error.status || '') + ': ' + (error.statusText || 'Error'), 'error');
    });
});
