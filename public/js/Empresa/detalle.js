'use strict';

(function () {
    function byId(id) {
        return document.getElementById(id);
    }

    function showModal(id) {
        if (window.intranetUiHelpers) {
            window.intranetUiHelpers.showModal(id);
            return;
        }

        var modalElement = byId(id);
        if (!modalElement) {
            return;
        }

        if (window.bootstrap && window.bootstrap.Modal) {
            window.bootstrap.Modal.getOrCreateInstance(modalElement).show();
            return;
        }

        if (window.jQuery) {
            window.jQuery(modalElement).modal('show');
        }
    }

    function hideModal(id) {
        if (window.intranetUiHelpers) {
            window.intranetUiHelpers.hideModal(id);
            return;
        }

        var modalElement = byId(id);
        if (!modalElement) {
            return;
        }

        if (window.bootstrap && window.bootstrap.Modal) {
            window.bootstrap.Modal.getOrCreateInstance(modalElement).hide();
            return;
        }

        if (window.jQuery) {
            window.jQuery(modalElement).modal('hide');
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
        var titleNode = modal.querySelector('h4.modal-title, h5.modal-title');
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

    function setInputValue(id, value) {
        var input = byId(id);
        if (input) {
            input.value = value || '';
        }
    }

    function getTrimmedAttribute(element, attributeName) {
        return (element.getAttribute(attributeName) || '').trim();
    }

    function buildCenterAddress(mapButton) {
        return [
            getTrimmedAttribute(mapButton, 'data-centro-direccio'),
            getTrimmedAttribute(mapButton, 'data-centro-codi-postal'),
            getTrimmedAttribute(mapButton, 'data-centro-localitat')
        ].filter(function (value) {
            return value !== '';
        }).join(', ');
    }

    function normalizeStreetAddress(address) {
        return address
            .replace(/^c\/\s*/i, 'Calle ')
            .replace(/^avda?\.?\s*/i, 'Avenida ')
            .replace(/\s+-\s+.*$/i, '')
            .replace(/,\s*(baix|bajo|local|planta|pis|piso|porta|puerta|esc\.?).*$/i, '')
            .replace(/\s+/g, ' ')
            .trim();
    }

    function pushUniqueAddress(addresses, address) {
        if (address && addresses.indexOf(address) === -1) {
            addresses.push(address);
        }
    }

    function buildCenterAddressVariants(mapButton) {
        var street = getTrimmedAttribute(mapButton, 'data-centro-direccio');
        var postalCode = getTrimmedAttribute(mapButton, 'data-centro-codi-postal');
        var locality = getTrimmedAttribute(mapButton, 'data-centro-localitat');
        var normalizedStreet = normalizeStreetAddress(street);
        var addresses = [];

        pushUniqueAddress(addresses, [street, postalCode, locality, 'España'].filter(Boolean).join(', '));
        pushUniqueAddress(addresses, [normalizedStreet, postalCode, locality, 'España'].filter(Boolean).join(', '));
        pushUniqueAddress(addresses, [normalizedStreet, locality, 'España'].filter(Boolean).join(', '));
        pushUniqueAddress(addresses, [postalCode, locality, 'España'].filter(Boolean).join(', '));

        return addresses;
    }

    function osmSearchUrl(address) {
        return 'https://www.openstreetmap.org/search?query=' + encodeURIComponent(address);
    }

    function nominatimSearchUrl(address) {
        return 'https://nominatim.openstreetmap.org/search?' + new URLSearchParams({
            format: 'jsonv2',
            q: address,
            limit: '1',
            countrycodes: 'es'
        }).toString();
    }

    function fetchNominatimAddress(address) {
        return fetch(nominatimSearchUrl(address), {
            headers: {
                Accept: 'application/json'
            }
        }).then(function (response) {
            if (!response.ok) {
                throw new Error('HTTP ' + response.status);
            }
            return response.json();
        }).then(function (results) {
            var result = Array.isArray(results) ? results[0] : null;
            if (!result || !result.lat || !result.lon) {
                return null;
            }

            return {
                address: address,
                result: result
            };
        });
    }

    function findFirstNominatimResult(addresses, index) {
        if (index >= addresses.length) {
            return Promise.resolve(null);
        }

        return fetchNominatimAddress(addresses[index]).then(function (payload) {
            if (payload) {
                return payload;
            }

            return findFirstNominatimResult(addresses, index + 1);
        });
    }

    function osmEmbedUrl(latitude, longitude) {
        var lat = parseFloat(latitude);
        var lon = parseFloat(longitude);
        var delta = 0.01;

        return 'https://www.openstreetmap.org/export/embed.html?' + new URLSearchParams({
            bbox: [
                (lon - delta).toFixed(5),
                (lat - delta).toFixed(5),
                (lon + delta).toFixed(5),
                (lat + delta).toFixed(5)
            ].join(','),
            layer: 'mapnik',
            marker: lat.toFixed(5) + ',' + lon.toFixed(5)
        }).toString();
    }

    function setMapMessage(message, type) {
        var messageNode = byId('mapaCentroMissatge');
        if (!messageNode) {
            return;
        }

        messageNode.className = 'alert alert-' + (type || 'info');
        messageNode.textContent = message;
        messageNode.style.display = message ? '' : 'none';
    }

    function clearMapFrame() {
        var frame = byId('mapaCentroFrame');
        if (frame) {
            frame.removeAttribute('src');
            frame.style.display = 'none';
        }
    }

    function showMapFrame(url) {
        var frame = byId('mapaCentroFrame');
        if (frame) {
            frame.src = url;
            frame.style.display = '';
        }
    }

    function openCenterMap(mapButton) {
        var name = getTrimmedAttribute(mapButton, 'data-centro-nom') || 'Centre de treball';
        var address = buildCenterAddress(mapButton);
        var addressVariants = buildCenterAddressVariants(mapButton);
        var addressNode = byId('mapaCentroDireccio');
        var externalLink = byId('mapaCentroEnllac');

        setModalTitle('MapaCentro', 'Mapa de ' + name);
        clearMapFrame();
        setMapMessage('', 'info');

        if (addressNode) {
            addressNode.textContent = address;
        }
        if (externalLink) {
            externalLink.href = address ? osmSearchUrl(addressVariants[0] || address) : '#';
            externalLink.style.display = address ? '' : 'none';
        }

        showModal('MapaCentro');

        if (!address) {
            setMapMessage('Este centre no té adreça disponible per a mostrar el mapa.', 'warning');
            return;
        }

        setMapMessage('Carregant el mapa del centre...', 'info');

        findFirstNominatimResult(addressVariants, 0).then(function (payload) {
            if (!payload) {
                setMapMessage('No s\'ha trobat cap ubicació per a esta adreça.', 'warning');
                return;
            }

            setMapMessage('', 'info');
            showMapFrame(osmEmbedUrl(payload.result.lat, payload.result.lon));
        }).catch(function (error) {
            window.console.error(error);
            setMapMessage('No s\'ha pogut carregar el mapa. Pots obrir la cerca en OpenStreetMap.', 'warning');
        });
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

            var mapaCentroBtn = event.target.closest('.mapa-centro');
            if (mapaCentroBtn) {
                event.preventDefault();
                openCenterMap(mapaCentroBtn);
                return;
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
                        setInputValue('idCentro', result.data.id);
                        setInputValue('nombreCentro', result.data.nombre);
                        setInputValue('telefonoCentro', result.data.telefono);
                        setInputValue('emailCentro', result.data.email);
                        setInputValue('horariosCentro', result.data.horarios);
                        setInputValue('observacionesCentro', result.data.observaciones);
                        setInputValue('codiPostalCentro', result.data.codiPostal);
                        setInputValue('direccionCentro', result.data.direccion);
                        setInputValue('localidadCentro', result.data.localidad);
                        setInputValue('idiomaCentro', result.data.idioma);
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
                request('POST', fusionarBtn.dataset.fusionUrl || '/api/centro/fusionar', {
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
