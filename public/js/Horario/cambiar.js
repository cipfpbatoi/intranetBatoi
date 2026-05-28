'use strict';

var esDireccion = false;
var profe = '';

function trim(value) {
    return (value || '').toString().trim();
}

function getToken() {
    var tokenNode = document.getElementById('_token');
    return trim(tokenNode ? tokenNode.textContent : '');
}

function getBearerToken() {
    var bearerMeta = document.querySelector('meta[name="user-bearer-token"]');
    return trim(bearerMeta ? bearerMeta.getAttribute('content') : '');
}

function apiAuthOptions(extraData) {
    var legacyToken = getToken();
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
        var isJson = (response.headers.get('content-type') || '').indexOf('application/json') !== -1;
        return (isJson ? response.json() : response.text().then(function (text) {
            return { raw: text };
        })).then(function (payload) {
            if (!response.ok) {
                var error = new Error('HTTP ' + response.status);
                error.status = response.status;
                error.statusText = response.statusText;
                error.response = payload;
                throw error;
            }
            return payload;
        });
    });
}

function setTdDraggable(td, enabled) {
    if (!td) {
        return;
    }

    td.style.cursor = enabled ? 'pointer' : 'default';
    td.draggable = enabled;
}

function onTdDragStart(ev) {
    if (!ev.currentTarget || !ev.currentTarget.id) {
        return;
    }
    ev.dataTransfer.setData('text/plain', ev.currentTarget.id);
}

function onTdDragOver(ev) {
    ev.preventDefault();
}

function dropHora(ev) {
    ev.preventDefault();

    var destino = ev.currentTarget;
    if (!destino || destino.children.length > 0) {
        return false;
    }

    var origenId = '';
    try {
        origenId = ev.dataTransfer.getData('text/plain');
    } catch (error) {
        console.error(error);
        return false;
    }

    var origen = document.getElementById(origenId);
    if (!origen || !/^\d{1,2}-(L|M|X|J|V)$/.test(origen.id)) {
        return false;
    }
    if (origen.id === destino.id) {
        return false;
    }

    var dato = origen.querySelector('div');
    if (!dato) {
        return false;
    }

    if (dato.getAttribute('data-orig') === destino.id) {
        dato.classList.remove('movido');
    } else {
        dato.classList.add('movido');
    }

    setTdDraggable(destino, true);
    destino.removeEventListener('dragstart', onTdDragStart);
    destino.addEventListener('dragstart', onTdDragStart);
    destino.appendChild(dato);

    setTdDraggable(origen, false);
    origen.removeEventListener('dragstart', onTdDragStart);
    origen.removeEventListener('drop', dropHora);
    origen.addEventListener('drop', dropHora);

    destino.removeEventListener('drop', dropHora);

    if (esDireccion) {
        var aplicarBtn = document.getElementById('aplicar');
        if (aplicarBtn) {
            aplicarBtn.hidden = true;
        }
    }
    return true;
}

function anotaCambios() {
    var cambios = [];

    Array.from(document.querySelectorAll('tbody td')).forEach(function (td) {
        var div = td.getElementsByTagName('div')[0];
        var origen = div ? div.getAttribute('data-orig') : '';
        if (div && origen && td.id && td.id !== origen) {
            cambios.push({ de: origen, a: td.id });
        }
    });

    return cambios;
}

function realizaCambios(cambios, modificable) {
    var datosOrig = [];

    cambios.forEach(function (cambio) {
        var celdaOrigen = document.getElementById(cambio.de);
        if (!celdaOrigen) {
            console.warn("No s'ha trobat la cel.la d'origen amb id: " + cambio.de);
            return;
        }

        var dato = celdaOrigen.querySelector('div');
        if (!dato) {
            console.warn("No s'ha trobat cap <div> dins de la cel.la d'origen amb id: " + cambio.de);
            return;
        }

        dato.classList.add('movido');
        datosOrig.push({ id: cambio.de, data: dato });
        var padre = dato.parentElement;
        if (modificable && padre) {
            setTdDraggable(padre, false);
            padre.removeEventListener('dragstart', onTdDragStart);
            padre.addEventListener('drop', dropHora);
        }
        if (padre) {
            padre.removeChild(dato);
        }
    });

    cambios.forEach(function (cambio) {
        var celdaDestino = document.getElementById(cambio.a);
        if (!celdaDestino) {
            console.warn("No s'ha trobat la cel.la de desti amb id: " + cambio.a);
            return;
        }

        var datoWrap = datosOrig.find(function (item) {
            return item.id === cambio.de;
        });
        if (!datoWrap || !datoWrap.data) {
            console.warn("No s'ha pogut trobar el <div> del canvi de " + cambio.de + ' a ' + cambio.a);
            return;
        }

        var dato = datoWrap.data;
        celdaDestino.appendChild(dato);

        if (modificable) {
            celdaDestino.removeEventListener('drop', dropHora);
            celdaDestino.removeEventListener('dragstart', onTdDragStart);
            celdaDestino.addEventListener('dragstart', onTdDragStart);
            setTdDraggable(celdaDestino, true);
        }

        if (dato.getAttribute('data-orig') === celdaDestino.id) {
            dato.classList.remove('movido');
        } else {
            dato.classList.add('movido');
        }
    });
}

function activaDragAndDrop() {
    var ocupadas = document.querySelectorAll('td.active, td.warning');
    ocupadas.forEach(function (td) {
        setTdDraggable(td, true);
        td.removeEventListener('dragstart', onTdDragStart);
        td.addEventListener('dragstart', onTdDragStart);
    });

    var celdas = document.querySelectorAll('table tbody td');
    celdas.forEach(function (td) {
        td.removeEventListener('dragover', onTdDragOver);
        td.addEventListener('dragover', onTdDragOver);
    });

    var vacias = document.querySelectorAll('table td:empty');
    vacias.forEach(function (td) {
        td.removeEventListener('drop', dropHora);
        td.addEventListener('drop', dropHora);
    });
}

function cargaCambios() {
    apiRequest('GET', '/api/horarioChange/' + profe).then(function (res) {
        var datos = {};
        try {
            datos = JSON.parse(res.data || '{}');
        } catch (e) {
            datos = {};
        }

        if (!datos.estado) {
            datos.estado = 'Pendiente';
        }

        var modificable = (datos.estado === 'Pendiente' || esDireccion);
        if (modificable) {
            activaDragAndDrop();
        } else {
            var guardar = document.getElementById('guardar');
            var obs = document.getElementById('obs');
            if (guardar) {
                guardar.disabled = true;
            }
            if (obs) {
                obs.disabled = true;
            }
            alert('Tu horario ya ha sido aceptado por dirección y no lo puedes modificar');
        }

        realizaCambios(datos.cambios || [], modificable);

        var estado = document.getElementById('estado');
        var obsInput = document.getElementById('obs');
        if (estado) {
            estado.value = datos.estado;
        }
        if (obsInput) {
            obsInput.value = datos.obs || '';
        }

        if (datos.estado === 'Aceptado' && esDireccion) {
            var aplicar = document.getElementById('aplicar');
            if (aplicar) {
                aplicar.hidden = false;
                aplicar.addEventListener('click', function () {
                    if (confirm('Deseas aplicar ya estos cambios al horario del profesor?')) {
                        location.href = '/profesor/' + profe + '/horario-aceptar';
                    }
                });
            }
        }
    }, function (err) {
        var message = err && err.response && err.response.message ? err.response.message : '';
        if (message === 'No hi ha fitxer') {
            activaDragAndDrop();
            var estado = document.getElementById('estado');
            var obs = document.getElementById('obs');
            if (estado) {
                estado.value = 'No hay propuesta';
            }
            if (obs) {
                obs.value = '';
            }
        } else {
            alert((err && err.response && err.response.raw) || err.statusText || 'Error carregant canvis');
        }
    });
}

document.addEventListener('DOMContentLoaded', function () {
    var rolNode = document.getElementById('rol');
    var rol = parseInt(trim(rolNode ? rolNode.textContent : '0'), 10) || 0;
    esDireccion = (rol % 2 === 0);

    if (esDireccion) {
        var guardarBtn = document.getElementById('guardar');
        if (guardarBtn) {
            guardarBtn.textContent = 'Aprobar horario';
        }
        var pathParts = location.pathname.split('/');
        profe = pathParts[2] || '';
    } else {
        var dniNode = document.getElementById('dni');
        profe = trim(dniNode ? dniNode.textContent : '');
    }

    cargaCambios();

    var initBtn = document.getElementById('init');
    if (initBtn) {
        initBtn.addEventListener('click', function (ev) {
            ev.preventDefault();
            var datos = {
                estado: 'Pendiente',
                cambios: [],
                obs: ''
            };
            apiRequest('POST', '/api/horarioChange/' + profe, { data: JSON.stringify(datos) }).then(function () {
                alert("Ja pots modificar l'horari");
                location.reload();
            }, function (err) {
                alert('Error al guardar los datos: ' + (err.statusText || 'Error'));
            });
        });
    }

    var guardar = document.getElementById('guardar');
    if (guardar) {
        guardar.addEventListener('click', function (ev) {
            ev.preventDefault();
            var cambios = anotaCambios();
            var obs = document.getElementById('obs');
            var datos = {
                estado: esDireccion ? 'Aceptado' : 'Pendiente',
                cambios: cambios,
                obs: obs ? obs.value : ''
            };

            apiRequest('POST', '/api/horarioChange/' + profe, { data: JSON.stringify(datos) }).then(function (res) {
                if (esDireccion) {
                    alert((res.data || '') + '. Horario aprobado');
                    location.reload();
                } else {
                    alert((res.data || '') + '. Tu nuevo horario te aparecera cuando este aprobado');
                    location.href = '/home';
                }
            }, function (err) {
                alert('Error al guardar los datos: ' + (err.statusText || 'Error'));
            });
        });
    }
});
