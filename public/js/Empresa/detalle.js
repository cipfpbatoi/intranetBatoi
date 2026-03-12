'use strict';

(function () {
    function byId(id) {
        return document.getElementById(id);
    }

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

    function withQueryParams(url, params) {
        var query = new URLSearchParams(params || {}).toString();
        if (!query) {
            return url;
        }
        return url + (url.indexOf('?') === -1 ? '?' : '&') + query;
    }

    function toFormBody(data) {
        var params = new URLSearchParams();
        Object.keys(data || {}).forEach(function (key) {
            var value = data[key];
            if (Array.isArray(value)) {
                value.forEach(function (item) {
                    params.append(key + '[]', String(item));
                });
                return;
            }
            if (value !== undefined && value !== null) {
                params.append(key, String(value));
            }
        });
        return params.toString();
    }

    function request(method, url, data, expectJson) {
        var auth = apiAuthOptions(data || {});
        var options = {
            method: method,
            headers: Object.assign({}, auth.headers),
            credentials: 'same-origin'
        };
        var finalUrl = url;

        if (method === 'GET' || method === 'DELETE') {
            finalUrl = withQueryParams(url, auth.data);
        } else {
            options.headers['Content-Type'] = 'application/x-www-form-urlencoded; charset=UTF-8';
            options.body = toFormBody(auth.data);
        }

        return fetch(finalUrl, options).then(function (response) {
            if (!response.ok) {
                return response.text().then(function (text) {
                    var error = new Error('HTTP ' + response.status);
                    error.status = response.status;
                    error.statusText = response.statusText;
                    error.responseText = text;
                    throw error;
                });
            }

            if (expectJson === false) {
                return response.text();
            }
            return response.json();
        });
    }

    function setModalTitle(modalId, title) {
        var modal = byId(modalId);
        if (!modal) {
            return;
        }
        var titleNode = modal.querySelector('h4.modal-title');
        if (titleNode) {
            titleNode.textContent = title;
        }
    }

    function showError(error) {
        if (typeof showMessage === 'function') {
            showMessage(['Error ' + error.status + ': ' + error.statusText, 'error'], 'error');
            return;
        }
        window.console.error(error);
    }

    function getCheckedFusionValues() {
        var values = [];
        document.querySelectorAll('input[type="checkbox"]:checked').forEach(function (checkbox) {
            values.push(checkbox.value);
        });
        return values;
    }

    function wireGlobalEditar() {
        window.editar = function (id) {
            var form = byId('formAddEnterprise');
            if (form) {
                form.setAttribute('action', '/centro/' + id + '/empresa/create');
            }
            showModal('AddEnterprise');
        };
    }

    document.addEventListener('DOMContentLoaded', function () {
        wireGlobalEditar();

        var idCentro = byId('idCentro');
        var contacto = byId('contacto_id');
        var instructor = byId('instructor_id');

        if (contacto) {
            contacto.addEventListener('change', function () {
                if (instructor && instructor.value === '') {
                    instructor.innerHTML = '';
                    instructor.value = contacto.value;
                }
            });
        }

        document.addEventListener('click', function (event) {
            var addCol = event.target.closest('.addCol');
            if (addCol && idCentro) {
                idCentro.value = addCol.getAttribute('href') || '';
            }

            var dangerButton = event.target.closest('input.btn-sm.btn-danger');
            if (dangerButton) {
                window.confirm('Vas a crear una nova empresa a partir del centre de treball');
            }

            var editarCol = event.target.closest('.editar');
            if (editarCol) {
                event.preventDefault();
                var colId = editarCol.id;
                showModal('AddColaboration');
                setModalTitle('AddColaboration', 'Modificar Col·laboracio');

                request('GET', '/api/colaboracion/' + colId, {}, true)
                    .then(function (result) {
                        byId('idCiclo').value = result.data.idCiclo;
                        byId('idCiclo').disabled = true;
                        byId('idCentro').value = result.data.idCentro;
                        byId('idCentro').disabled = true;
                        byId('id').value = result.data.id;
                        byId('contacto_id').value = result.data.contacto;
                        byId('telefono').value = result.data.telefono;
                        byId('email').value = result.data.email;
                        byId('tutor').value = result.data.tutor;
                        byId('puestos').value = result.data.puestos;
                    })
                    .catch(showError);
                return;
            }

            var centroBtn = event.target.closest('.centro');
            if (centroBtn) {
                event.preventDefault();
                var centroId = centroBtn.id;
                showModal('AddCenter');
                setModalTitle('AddCenter', 'Modificar Centre Treball');

                request('GET', '/api/centro/' + centroId, {}, true)
                    .then(function (result) {
                        byId('idCentro').value = result.data.id;
                        byId('nombreCentro').value = result.data.nombre;
                        byId('telefonoCentro').value = result.data.telefono;
                        byId('emailCentro').value = result.data.email;
                        byId('horariosCentro').value = result.data.horarios;
                        byId('observacionesCentro').value = result.data.observaciones;
                        byId('codiPostalCentro').value = result.data.codiPostal;
                        byId('direccionCentro').value = result.data.direccion;
                        byId('localidadCentro').value = result.data.localidad;
                        byId('idiomaCentro').value = result.data.idioma;
                    })
                    .catch(showError);
                return;
            }

            var saveCenterBtn = event.target.closest('#AddCenter button.submit.btn.btn-primary');
            if (saveCenterBtn) {
                var centerId = byId('idCentro').value;
                if (!centerId) {
                    return;
                }
                event.preventDefault();

                request('PUT', '/api/centro/' + centerId, {
                    nombre: byId('nombreCentro').value,
                    telefono: byId('telefonoCentro').value,
                    email: byId('emailCentro').value,
                    direccion: byId('direccionCentro').value,
                    localidad: byId('localidadCentro').value,
                    observaciones: byId('observacionesCentro').value,
                    horarios: byId('horariosCentro').value,
                    codiPostal: byId('codiPostalCentro').value,
                    idioma: byId('idiomaCentro').value
                }, true).then(function () {
                    hideModal('AddCenter');
                    window.location.reload();
                }).catch(function (error) {
                    window.console.error(error);
                });
                return;
            }

            var saveColBtn = event.target.closest('#AddColaboration button.submit.btn.btn-primary');
            if (saveColBtn) {
                var editId = byId('id').value;
                if (!editId) {
                    return;
                }
                event.preventDefault();

                request('PUT', '/api/colaboracion/' + editId, {
                    contacto: byId('contacto_id').value,
                    telefono: byId('telefono').value,
                    email: byId('email').value,
                    puestos: byId('puestos').value,
                    tutor: byId('tutor').value
                }, true).then(function () {
                    hideModal('AddColaboration');
                    window.location.reload();
                }).catch(showError);
                return;
            }

            var fusionarBtn = event.target.closest('#fusionar');
            if (fusionarBtn) {
                event.preventDefault();
                request('POST', '/api/centro/fusionar', {
                    fusion: getCheckedFusionValues()
                }, true).then(function () {
                    window.location.reload();
                }).catch(function (error) {
                    window.console.log(['Error ' + error.status + ': ' + error.statusText, 'error'], 'error');
                });
            }
        });
    });
})();
