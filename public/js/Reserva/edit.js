'use strict';

const maxDiasReserva = 30;
const minDiasReserva = 3;
const esDireccion = 2;

let clickOrigin = 0;

function apiAuthOptions(extraData) {
    var bearerMeta = document.querySelector('meta[name="user-bearer-token"]');
    var bearerToken = ((bearerMeta ? bearerMeta.getAttribute('content') : '') || '').trim();
    var data = extraData || {};
    var headers = {};

    if (bearerToken) {
        headers.Authorization = 'Bearer ' + bearerToken;
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
        if (!response.ok) {
            var error = new Error('HTTP ' + response.status);
            error.status = response.status;
            error.statusText = response.statusText;
            throw error;
        }
        return parseJsonSafe(response);
    });
}

function getEl(id) {
    return document.getElementById(id);
}

function getRole() {
    var roleEl = getEl('rol');
    return parseInt(roleEl ? roleEl.textContent : '0', 10) || 0;
}

function isDireccionRole() {
    return getRole() % esDireccion === 0;
}

function getFechaInt(fechaDate) {
    var mes = '0' + (fechaDate.getMonth() + 1);
    mes = mes.substr(mes.length - 2, 2);
    var dia = '0' + fechaDate.getDate();
    dia = dia.substr(dia.length - 2, 2);
    return fechaDate.getFullYear() + '-' + mes + '-' + dia;
}

function setGestionVisible(visible) {
    var gestion = getEl('gestion');
    if (gestion) {
        gestion.style.display = visible ? '' : 'none';
    }
}

function clearHorario() {
    var celdas = document.querySelectorAll('#horario td');
    celdas.forEach(function (cell) {
        cell.classList.remove('warning');
        cell.innerHTML = 'Lliure';
    });
}

function setDayLabels(fecha) {
    var nomDias = ['Diumenge', 'Dilluns', 'Dimarts', 'Dimecres', 'Dijous', 'Divendres', 'Dissabte'];
    var diaSetmana = nomDias[fecha.getDay()];

    var nomDiaFin = getEl('nom_dia_fin');
    if (nomDiaFin) {
        nomDiaFin.textContent = diaSetmana;
    }

    var nomDiaSemana = getEl('nom_dia_semana');
    if (nomDiaSemana) {
        nomDiaSemana.textContent = diaSetmana;
    }
}

function triggerDiaChange() {
    var diaInput = getEl('dia');
    if (diaInput) {
        diaInput.dispatchEvent(new Event('change', { bubbles: true }));
    }
}

function shiftDia(days) {
    var diaInput = getEl('dia');
    if (!diaInput || !diaInput.value) {
        return;
    }

    var queFecha = new Date(diaInput.value);
    queFecha.setDate(queFecha.getDate() + days);
    diaInput.value = getFechaInt(queFecha);
    triggerDiaChange();
}

function updateHastaOptions() {
    var desde = getEl('desde');
    var hasta = getEl('hasta');
    if (!desde || !hasta) {
        return;
    }

    var desdeIndex = desde.selectedIndex;
    hasta.value = desde.value;
    Array.from(hasta.options).forEach(function (option, idx) {
        option.disabled = idx < desdeIndex;
    });

    if (parseInt(desde.value, 10) > 0) {
        hasta.value = desde.value;
    } else {
        desde.value = '0';
    }
}

function getRangeCells() {
    var desde = parseInt(getEl('desde') ? getEl('desde').value : '0', 10) || 0;
    var hasta = parseInt(getEl('hasta') ? getEl('hasta').value : '0', 10) || 0;
    var celdas = Array.from(document.querySelectorAll('#horario td'));
    if (desde <= 0 || hasta <= 0 || hasta < desde) {
        return [];
    }
    return celdas.slice(desde - 1, hasta);
}

function marcar() {
    document.querySelectorAll('#horario td').forEach(function (cell) {
        cell.classList.remove('green');
        cell.classList.remove('red');
    });

    getRangeCells().forEach(function (cell) {
        if ((cell.textContent || '').trim() === 'Lliure') {
            cell.classList.add('green');
        } else {
            cell.classList.add('red');
        }
    });
}

function showMessage(msgs, tipo) {
    var errores = getEl('errores');
    if (!errores) {
        return;
    }

    var alertClass = tipo === 'error' ? 'alert-danger' : 'alert-success';
    var html = '<div id="msg" class="alert ' + alertClass + ' alert-block fade in">';
    html += '<button type="button" class="close" data-dismiss="alert">×</button>';
    msgs.forEach(function (msg) {
        html += '<p><strong>' + msg + '</strong></p>';
    });
    html += '</div>';
    errores.innerHTML = html;
}

function checkData() {
    var errores = [];
    var recurso = getEl('recurso');
    var observaciones = getEl('observaciones');
    var dia = getEl('dia');
    var desde = getEl('desde');
    var hasta = getEl('hasta');
    var diaFin = getEl('dia_fin');

    if (!recurso || recurso.value === '0') {
        errores.push("el camp 'Recurs' ha d'estar seleccionat");
    }
    if (observaciones && observaciones.value.length > 20) {
        errores.push("el camp 'Observacions' té un màxim de 20 caracters");
    }

    var fecha = dia ? dia.value : '';
    if (!fecha) {
        errores.push("el camp 'Dia' ha d'estar seleccionat");
    }

    var desdeVal = parseInt(desde ? desde.value : '0', 10) || 0;
    if (desdeVal === 0) {
        errores.push("el camp 'Des d'hora' ha d'estar seleccionat");
    }

    var hastaVal = parseInt(hasta ? hasta.value : '0', 10) || 0;
    if (hastaVal === 0) {
        errores.push("el camp 'fins hora' ha d'estar seleccionat");
    } else if (hastaVal < desdeVal) {
        errores.push("el camp 'fins hora' ha de ser major que 'des d'hora'");
    }

    var fechaFin = diaFin ? diaFin.value : '';
    if (fechaFin && fechaFin < fecha) {
        errores.push("el camp 'fins dia' ha de ser major que 'dia'");
    }

    if (errores.length > 0) {
        showMessage(errores, 'error');
        return false;
    }
    return true;
}

function loadReservas() {
    clearHorario();

    var diaInput = getEl('dia');
    var recurso = getEl('recurso');
    if (!diaInput || !recurso) {
        return;
    }

    var fecha = diaInput.value;
    if (!fecha) {
        return;
    }

    var queFecha = new Date(fecha);
    setDayLabels(queFecha);

    apiRequest('GET', 'api/reserva', {
        idEspacio: recurso.value,
        dia: fecha
    }).then(function (res) {
        (res.data || []).forEach(function (item) {
            var observaciones = item.observaciones ? '(' + item.observaciones + ')' : ' ';
            var horaCell = getEl('hora-' + item.hora);
            if (horaCell) {
                horaCell.classList.add('warning');
                horaCell.innerHTML = item.nomProfe + observaciones
                    + '<span class="hidden idProfe">' + item.idProfesor + '</span>'
                    + '<span class="hidden idReserva">' + item.id + '</span>';
            }
        });
    }, function (error) {
        showMessage(['Error ' + (error.status || '') + ': ' + (error.statusText || 'Error')], 'error');
    });
}

function modDatos(accion) {
    var dia = getEl('dia');
    var recurso = getEl('recurso');
    var idProfesor = getEl('idProfesor');
    var observaciones = getEl('observaciones');
    var desde = getEl('desde');
    var hasta = getEl('hasta');
    var diaFin = getEl('dia_fin');
    var dni = getEl('dni');

    if (!dia || !recurso || !desde || !hasta) {
        return;
    }

    var fecha = dia.value;
    var fechaDate = new Date(fecha);
    var peticiones = [];
    var msg = accion === 'reserva'
        ? 'Se va a reservar el recurso entre las fechas indicadas. Si hubiera alguna hora ya reservada fallará la reserva. ¿Deseas continuar?'
        : 'Se van a liberar todas las reservas del recurso entre las fechas indicadas ¿Deseas continuar?';

    if (!confirm(msg)) {
        return false;
    }

    if (accion === 'reserva') {
        var datosBase = {
            idEspacio: recurso.value,
            idProfesor: idProfesor ? idProfesor.value : '',
            observaciones: observaciones ? observaciones.value : ''
        };
        var fechaFinDate = diaFin && diaFin.value ? new Date(diaFin.value) : new Date(fecha);

        while (fechaDate <= fechaFinDate) {
            for (var i = Number(desde.value); i <= Number(hasta.value); i += 1) {
                peticiones.push({ fecha: fecha, hora: i });
            }
            fechaDate.setDate(fechaDate.getDate() + 7);
            fecha = getFechaInt(fechaDate);
        }

        if (peticiones.length === 0) {
            showMessage(['No hi ha hores per a processar'], 'error');
            return false;
        }

        Promise.allSettled(peticiones.map(function (peticion) {
            return apiRequest('POST', 'api/reserva', Object.assign({}, datosBase, {
                dia: peticion.fecha,
                hora: peticion.hora
            }));
        })).then(function (results) {
            var hasErrors = results.some(function (result) {
                if (result.status !== 'fulfilled') {
                    return true;
                }
                if (result.value && Object.prototype.hasOwnProperty.call(result.value, 'success')) {
                    return result.value.success !== true;
                }
                return false;
            });

            if (hasErrors) {
                showMessage(['Algunas horas no se han podido reservar'], 'error');
            } else {
                showMessage(['El recurso se ha reservado correctamente'], 'ok');
            }
            triggerDiaChange();
        });
    } else {
        for (var j = Number(desde.value); j <= Number(hasta.value); j += 1) {
            var horaCell = getEl('hora-' + j);
            var reservaSpan = horaCell ? horaCell.querySelector('span.idReserva') : null;
            if (reservaSpan && reservaSpan.textContent) {
                peticiones.push({ hora: reservaSpan.textContent });
            }
        }

        if (peticiones.length === 0) {
            showMessage(['No hi ha hores per a processar'], 'error');
            return false;
        }

        Promise.allSettled(peticiones.map(function (peticion) {
            return apiRequest('DELETE', 'api/reserva/' + peticion.hora, {});
        })).then(function (results) {
            var hasErrors = results.some(function (result) {
                if (result.status !== 'fulfilled') {
                    return true;
                }
                if (result.value && Object.prototype.hasOwnProperty.call(result.value, 'success')) {
                    return result.value.success !== true;
                }
                return false;
            });

            if (hasErrors) {
                showMessage(['Algunas horas no se han podido liberar'], 'error');
            } else {
                showMessage(['El recurso se ha liberado correctamente'], 'ok');
            }
            triggerDiaChange();
        });
    }

    marcar();
    return true;
}

document.addEventListener('DOMContentLoaded', function () {
    var erroresContainer = document.querySelector('.errores');
    if (erroresContainer) {
        erroresContainer.innerHTML = '';
    }

    var recurso = getEl('recurso');
    var gestion = getEl('gestion');
    var dia = getEl('dia');
    var periodica = getEl('periodica');
    var observaciones = getEl('observaciones');
    var desde = getEl('desde');
    var hasta = getEl('hasta');
    var reservar = getEl('reservar');
    var liberar = getEl('liberar');

    if (recurso) {
        recurso.focus();
    }
    if (gestion) {
        setGestionVisible(false);
    }

    if (dia) {
        var maxFecha = new Date();
        maxFecha.setDate(maxFecha.getDate() - minDiasReserva);
        dia.setAttribute('minDate', maxFecha.toISOString().substr(0, 10));

        if (isDireccionRole()) {
            dia.removeAttribute('max');
            if (periodica) {
                periodica.style.display = '';
            }
        } else {
            maxFecha = new Date();
            maxFecha.setDate(maxFecha.getDate() + maxDiasReserva);
            dia.setAttribute('max', maxFecha.toISOString().substr(0, 10));
            if (periodica) {
                periodica.style.display = 'none';
            }
        }
    }

    if (recurso) {
        recurso.addEventListener('change', function () {
            var selected = recurso.value !== '0';
            setGestionVisible(selected);
            if (selected && dia && dia.value) {
                triggerDiaChange();
            }
        });
    }

    var horaCells = document.querySelectorAll('.hora');
    horaCells.forEach(function (cell) {
        cell.addEventListener('click', function (ev) {
            ev.preventDefault();
            var hora = this.id.split('-')[1];
            var desdeSelect = getEl('desde');
            var hastaSelect = getEl('hasta');
            if (!desdeSelect || !hastaSelect) {
                return;
            }

            var desdeVal = parseInt(desdeSelect.value, 10) || 0;
            hastaSelect.value = hora;
            if (desdeVal === 0 || parseInt(hora, 10) < desdeVal) {
                desdeSelect.value = hora;
            }
            marcar();
        });
    });

    var navMap = {
        next: 1,
        back: -1,
        forward: 7,
        reward: -7
    };
    Object.keys(navMap).forEach(function (id) {
        var btn = getEl(id);
        if (btn) {
            btn.addEventListener('click', function (ev) {
                ev.preventDefault();
                shiftDia(navMap[id]);
            });
        }
    });

    if (dia) {
        dia.addEventListener('change', function () {
            loadReservas();
        });
    }

    if (desde) {
        desde.addEventListener('change', function () {
            updateHastaOptions();
            marcar();
            clickOrigin = 1;
        });
    }

    if (hasta) {
        hasta.addEventListener('change', function () {
            marcar();
            clickOrigin = 0;
        });
    }

    if (reservar) {
        reservar.addEventListener('click', function (ev) {
            ev.preventDefault();
            var errores = [];

            if (checkData()) {
                getRangeCells().forEach(function (cell) {
                    if ((cell.textContent || '').trim() !== 'Lliure') {
                        var horaText = cell.parentElement && cell.parentElement.firstElementChild
                            ? cell.parentElement.firstElementChild.textContent
                            : '';
                        errores.push("En l'interval indicat no està lliure l'hora " + horaText);
                    }
                });
                if (errores.length === 0) {
                    modDatos('reserva');
                } else {
                    showMessage(errores, 'error');
                }
            }
        });
    }

    if (liberar) {
        liberar.addEventListener('click', function (ev) {
            ev.preventDefault();
            var errores = [];

            if (checkData()) {
                if (isDireccionRole()) {
                    modDatos('libera');
                } else {
                    var dniValue = (getEl('dni') ? getEl('dni').textContent : '').trim();
                    getRangeCells().forEach(function (cell) {
                        var idProfe = cell.querySelector('span.idProfe');
                        var reservaDni = (idProfe ? idProfe.textContent : '').trim();
                        if ((cell.textContent || '').trim() !== 'Lliure' && reservaDni !== dniValue) {
                            var horaText = cell.parentElement && cell.parentElement.firstElementChild
                                ? cell.parentElement.firstElementChild.textContent
                                : '';
                            errores.push("En l'interval indicat l'hora " + horaText + ' està reservada per ' + (cell.textContent || '').trim());
                        }
                    });
                    if (errores.length === 0) {
                        modDatos('libera');
                    } else {
                        showMessage(errores, 'error');
                    }
                }
            }
        });
    }

    if (observaciones) {
        observaciones.focus();
    }
});
