'use strict';

var id;
var col;
var list;
var day;
var month;
var tipo;
var studentContactList;
var studentContactLink;

function getApiAuth() {
    return window.intranetApiAuth || {};
}

function trim(value) {
    return (value || '').toString().trim();
}

function escapeHtml(value) {
    var container = document.createElement('div');
    container.textContent = value || '';
    return container.innerHTML;
}

function bindDraggableLink(link) {
    if (!link) {
        return;
    }
    link.setAttribute('draggable', 'draggable');
    link.addEventListener('dragstart', function (event) {
        event.dataTransfer.setData('text/plain', event.target.id);
    });
}

function appendActivityLink(listTarget, activityId, iconClass, comment) {
    day = new Date();
    month = day.getMonth() + 1;
    var hasComment = trim(comment).length > 0;
    var stateIcon = hasComment ? 'plus' : 'minus';
    var safeComment = escapeHtml(comment);

    var html = "<small><a href='#' class='small dragable' id='" + activityId +
        "' draggable='draggable' data-toggle='modal' data-target='#dialogo' title='" + safeComment + "'>" +
        "<em class='fa fa-" + stateIcon + "'></em> " + day.getDate() + "/" + month +
        " <em class='fa fa-" + iconClass + "'></em></a></small><br/>";

    listTarget.insertAdjacentHTML('beforeend', html);
    bindDraggableLink(document.getElementById(String(activityId)));
}

function apiRequest(method, url, extraData) {
    var apiAuth = getApiAuth();
    var auth = typeof apiAuth.apiAuthOptions === 'function'
        ? apiAuth.apiAuthOptions(extraData)
        : { headers: {}, data: extraData || {} };
    var options = {
        method: method,
        headers: Object.assign({}, auth.headers),
        credentials: 'same-origin'
    };

    if (method === 'GET') {
        url = typeof apiAuth.withQueryParams === 'function'
            ? apiAuth.withQueryParams(url, auth.data)
            : url;
    } else {
        options.headers['Content-Type'] = 'application/x-www-form-urlencoded; charset=UTF-8';
        options.body = new URLSearchParams(auth.data).toString();
    }

    return fetch(url, options).then(function (response) {
        if (!response.ok) {
            throw response;
        }

        return response.json();
    });
}

function setModalText(value) {
    var explanationField = document.querySelector('#dialogo #explicacion');
    if (explanationField) {
        explanationField.value = value || '';
    }
}

function getLastPhoneLink(container) {
    if (!container) {
        return null;
    }
    var links = Array.prototype.slice.call(container.querySelectorAll('a.small'));
    for (var i = links.length - 1; i >= 0; i -= 1) {
        if (links[i].querySelector('em.fa-phone')) {
            return links[i];
        }
    }
    return null;
}

function toggleCommentIcon(iconElement, hasComment) {
    if (!iconElement) {
        return;
    }
    iconElement.classList.toggle('fa-plus', hasComment);
    iconElement.classList.toggle('fa-minus', !hasComment);
}

function buildOption(value, text, disabled, selected) {
    var option = document.createElement('option');
    option.value = value || '';
    option.textContent = text || '';
    option.disabled = !!disabled;
    option.selected = !!selected;
    return option;
}

function setAlumnoOptions(options) {
    var select = document.getElementById('alumnoFct');
    if (!select) {
        return null;
    }

    select.innerHTML = '';
    (options || []).forEach(function (item) {
        select.appendChild(item);
    });

    return select;
}

function loadAlumnat(fctId) {
    setAlumnoOptions([
        buildOption('', 'Carregant alumnat...', true, true)
    ]);

    apiRequest('GET', '/fct/' + fctId + '/alFct')
        .then(function (response) {
            var fctAl = response.data || [];
            var select = setAlumnoOptions([]);
            if (!select) {
                return;
            }

            if (!fctAl.length) {
                setAlumnoOptions([
                    buildOption('', 'Sense alumnat disponible', true, true)
                ]);
                return;
            }

            fctAl.forEach(function (alumne) {
                var option = buildOption(alumne.id, alumne.nombre, false, false);
                option.setAttribute('data-short-name', alumne.nombre_corto || alumne.nombre || '');
                select.appendChild(option);
            });
        })
        .catch(function () {
            setAlumnoOptions([
                buildOption('', 'No s\'ha pogut carregar l\'alumnat', true, true)
            ]);
        });
}

function appendStudentContact(contacte, alumnoFctName) {
    if (!studentContactList) {
        return;
    }

    var emptyText = studentContactList.querySelector('.text-muted');
    if (emptyText) {
        emptyText.remove();
    }

    var hasComment = trim(contacte.comentari).length > 0;
    var icon = hasComment ? 'plus' : 'minus';
    var createdAt = contacte.created_at ? new Date(contacte.created_at) : new Date();
    var date = createdAt.getDate() + '/' + (createdAt.getMonth() + 1);
    var html = "<small><a href='#' class='small' id='" + contacte.id + "' title='" +
        escapeHtml(contacte.comentari || '') + "'>" +
        "<em class='fa fa-" + icon + "'></em> " + date + " " +
        escapeHtml(alumnoFctName || '') + "</a></small><br/>";

    studentContactList.insertAdjacentHTML('beforeend', html);
}

function setAlumnoModalText(value) {
    var form = document.getElementById('formDialogo_alumno');
    if (form && form.explicacion) {
        form.explicacion.value = value || '';
    }
}

document.addEventListener('DOMContentLoaded', function () {
    document.addEventListener('click', function (event) {
        var alumnatButton = event.target.closest('.alumnat');
        if (alumnatButton) {
            event.preventDefault();
            id = alumnatButton.getAttribute('data-fct-id') || '';
            tipo = 'alumnat-create';
            studentContactLink = null;
            var alumnatProfileView = alumnatButton.closest('.profile_view');
            studentContactList = alumnatProfileView ? alumnatProfileView.querySelector('.studentActivityList') : null;
            setAlumnoModalText('');
            if (window.intranetUiHelpers) {
                window.intranetUiHelpers.showModal('dialogo_alumno');
            }
            if (id) {
                loadAlumnat(id);
            }
            return;
        }

        var studentMinusIcon = event.target.closest('.studentActivity a.small .fa-minus');
        if (studentMinusIcon) {
            event.preventDefault();
            event.stopPropagation();
            var studentActivityLink = studentMinusIcon.closest('a.small');
            var studentActivityId = studentActivityLink ? studentActivityLink.id : '';
            if (studentActivityId && confirm('Vas a esborrar este contacte amb alumnat')) {
                apiRequest('DELETE', '/activity/' + studentActivityId).then(function () {
                    var small = studentMinusIcon.closest('small');
                    if (!small) {
                        return;
                    }
                    var nextSibling = small.nextElementSibling;
                    if (nextSibling && nextSibling.tagName === 'BR') {
                        nextSibling.remove();
                    }
                    small.remove();
                });
            }
            return;
        }

        var studentActivityAnchor = event.target.closest('.studentActivity a.small');
        if (studentActivityAnchor) {
            event.preventDefault();
            id = studentActivityAnchor.id;
            tipo = 'alumnat-edit';
            studentContactLink = studentActivityAnchor;
            studentContactList = studentActivityAnchor.closest('.studentActivityList');

            var studentLabel = trim(studentActivityAnchor.textContent);
            setAlumnoOptions([
                buildOption('', studentLabel, true, true)
            ]);
            setAlumnoModalText('');
            if (window.intranetUiHelpers) {
                window.intranetUiHelpers.showModal('dialogo_alumno');
            }

            apiRequest('GET', '/activity/' + id).then(function (result) {
                setAlumnoModalText((result.data && result.data.comentari) || '');
            }, function () {
                console.log("No s'ha pogut carregar el comentari de l'alumne.");
            });
            return;
        }

        var telefonicButton = event.target.closest('.telefonico');
        if (telefonicButton) {
            event.preventDefault();
            var profileView = telefonicButton.closest('.profile_view');
            var fctElement = profileView ? profileView.querySelector('.fct') : null;
            id = fctElement ? fctElement.id : '';
            list = profileView ? profileView.querySelector('.listActivity') : null;
            tipo = 'telefonico';
            setModalText('');
            if (window.intranetUiHelpers) {
                window.intranetUiHelpers.showModal('dialogo');
            }

            var todayPhone = getLastPhoneLink(list);
            if (todayPhone && todayPhone.id) {
                apiRequest('GET', '/api/fct/contact/' + todayPhone.id).then(function (result) {
                    setModalText((result.data && result.data.comentari) || '');
                }, function () {
                    console.log("No s'ha pogut carregar el comentari telefonic existent.");
                });
            }
            return;
        }

        var minusIcon = event.target.closest('.listActivity a.small .fa-minus');
        if (minusIcon) {
            event.preventDefault();
            event.stopPropagation();
            var activityLink = minusIcon.closest('a.small');
            var activityId = activityLink ? activityLink.id : '';
            if (activityId && confirm('Vas a esborrar esta evidencia')) {
                apiRequest('DELETE', '/activity/' + activityId).then(function () {
                    var small = minusIcon.closest('small');
                    if (!small) {
                        return;
                    }
                    var nextSibling = small.nextElementSibling;
                    if (nextSibling && nextSibling.tagName === 'BR') {
                        nextSibling.remove();
                    }
                    small.remove();
                });
            }
            return;
        }

        var activityAnchor = event.target.closest('.listActivity a.small');
        if (activityAnchor) {
            event.preventDefault();
            id = activityAnchor.id;
            tipo = 'seguimiento';
            setModalText('');
            if (window.intranetUiHelpers) {
                window.intranetUiHelpers.showModal('dialogo');
            }

            apiRequest('GET', '/api/fct/contact/' + id).then(function (result) {
                setModalText((result.data && result.data.comentari) || '');
            }, function () {
                console.log('Error al buscar');
            });
        }
    });

    var formDialogo = document.getElementById('formDialogo');
    if (formDialogo) {
        formDialogo.addEventListener('submit', function (event) {
            event.preventDefault();
            if (tipo === 'telefonico') {
                var comentariTelefonic = this.explicacion.value;
                apiRequest('POST', '/api/fct/' + id + '/telefonico', { explicacion: comentariTelefonic }).then(function (result) {
                    var targetId = String(result.data.id);
                    var existing = list ? list.querySelector("a.small[id='" + targetId + "']") : null;
                    if (existing) {
                        var hasComment = trim(comentariTelefonic).length > 0;
                        toggleCommentIcon(existing.querySelector('em.fa'), hasComment);
                        existing.setAttribute('title', comentariTelefonic);
                        existing.setAttribute('data-original-title', comentariTelefonic);
                    } else if (list) {
                        appendActivityLink(list, result.data.id, 'phone', comentariTelefonic);
                    }
                    if (window.intranetUiHelpers) {
                        window.intranetUiHelpers.hideModal('dialogo');
                    }
                }, function () {
                    console.log('Només es pot un per dia');
                    if (window.intranetUiHelpers) {
                        window.intranetUiHelpers.hideModal('dialogo');
                    }
                });
            }
            if (tipo === 'seguimiento') {
                var comentariActualitzat = this.explicacion.value;
                apiRequest('PUT', '/api/fct/contact/' + id, { explicacion: comentariActualitzat }).then(function () {
                    var link = document.getElementById(id);
                    var hasComment = trim(comentariActualitzat).length > 0;
                    toggleCommentIcon(link ? link.querySelector('em.fa') : null, hasComment);
                    if (link) {
                        link.setAttribute('title', comentariActualitzat);
                        link.setAttribute('data-original-title', comentariActualitzat);
                    }
                    if (window.intranetUiHelpers) {
                        window.intranetUiHelpers.hideModal('dialogo');
                    }
                }, function () {
                    console.log('Error al modificar');
                    if (window.intranetUiHelpers) {
                        window.intranetUiHelpers.hideModal('dialogo');
                    }
                });
            }
        });
    }

    var formDialogoAlumno = document.getElementById('formDialogo_alumno');
    if (formDialogoAlumno) {
        formDialogoAlumno.addEventListener('submit', function (event) {
            event.preventDefault();

            if (tipo === 'alumnat-edit') {
                var comentariActualitzat = formDialogoAlumno.explicacion ? formDialogoAlumno.explicacion.value : '';
                apiRequest('PUT', '/activity/' + id, { explicacion: comentariActualitzat }).then(function () {
                    var hasComment = trim(comentariActualitzat).length > 0;
                    toggleCommentIcon(studentContactLink ? studentContactLink.querySelector('em.fa') : null, hasComment);
                    if (studentContactLink) {
                        studentContactLink.setAttribute('title', comentariActualitzat);
                        studentContactLink.setAttribute('data-original-title', comentariActualitzat);
                    }
                    if (window.intranetUiHelpers) {
                        window.intranetUiHelpers.hideModal('dialogo_alumno');
                    }
                }, function () {
                    console.log('Error al modificar el contacte amb alumnat');
                    if (window.intranetUiHelpers) {
                        window.intranetUiHelpers.hideModal('dialogo_alumno');
                    }
                });
                return;
            }

            var alumnoFctSelect = formDialogoAlumno.alumnoFct;
            if (!alumnoFctSelect || !alumnoFctSelect.value) {
                return;
            }

            var alumnoFctId = alumnoFctSelect.value;
            var selectedOption = alumnoFctSelect.options[alumnoFctSelect.selectedIndex];
            var alumnoFctName = selectedOption
                ? selectedOption.getAttribute('data-short-name') || selectedOption.text
                : '';

            apiRequest('POST', '/fct/' + alumnoFctId + '/alFct', {
                explicacion: formDialogoAlumno.explicacion ? formDialogoAlumno.explicacion.value : ''
            }).then(function (result) {
                appendStudentContact(result.data, alumnoFctName);
                if (window.intranetUiHelpers) {
                    window.intranetUiHelpers.hideModal('dialogo_alumno');
                }
            }, function () {
                console.log('Només es pot un per dia');
                if (window.intranetUiHelpers) {
                    window.intranetUiHelpers.hideModal('dialogo_alumno');
                }
            });
        });
    }

    Array.from(document.querySelectorAll('.dragable')).forEach((item) => {
        bindDraggableLink(item);
    });
    Array.from(document.querySelectorAll('.fct')).forEach((item)=>{
        item.addEventListener('dragover',(event)=>{
            event.preventDefault();
        });
        item.addEventListener('drop',(event)=>{
            event.preventDefault();
            let id = event.dataTransfer.getData('text/plain');
            let newFct = event.currentTarget;
            const node = document.getElementById(id).parentElement;
            const nodeCopy = node.cloneNode(true);
            if (confirm('Vas a copiar esta evidencia a una altra FCT')){
                apiRequest('GET', "/activity/"+id+"/move/" + newFct.id).then(function (result) {
                    nodeCopy.firstElementChild.id = result.data.id;
                    nodeCopy.firstElementChild.firstElementChild.classList.remove('fa-plus');
                    nodeCopy.firstElementChild.firstElementChild.classList.add('fa-minus');
                    newFct.querySelector('.listActivity').appendChild(nodeCopy);
                    location.reload();
                }, function (result) {
                    alert("La sol·licitut no s'ha pogut completar: " + (result.statusText || 'Error'));
                });
            }
        });
    });
});
