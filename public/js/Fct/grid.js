'use strict';

var id;
var col;
var list;
var day;
var month;
var tipo;

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

document.addEventListener('DOMContentLoaded', function () {
    document.addEventListener('click', function (event) {
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
                apiRequest('GET', '/activity/' + todayPhone.id).then(function (result) {
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

            apiRequest('GET', '/activity/' + id).then(function (result) {
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
                apiRequest('POST', '/fct/' + id + '/telefonico', { explicacion: comentariTelefonic }).then(function (result) {
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
                apiRequest('PUT', '/activity/' + id, { comentari: comentariActualitzat }).then(function () {
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
