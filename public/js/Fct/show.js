'use strict';

(function () {
    function getHelpers() {
        return window.intranetUiHelpers || {};
    }

    function trim(value) {
        return (value || '').toString().trim();
    }

    function getApiAuthOptions(extraData) {
        if (typeof window.apiAuthOptions === 'function') {
            return window.apiAuthOptions(extraData);
        }

        var tokenElement = document.querySelector('#_token');
        var legacyToken = trim(tokenElement ? tokenElement.textContent : '');
        var bearerMeta = document.querySelector('meta[name="user-bearer-token"]');
        var bearerToken = trim(bearerMeta ? bearerMeta.getAttribute('content') : '');
        var csrfMeta = document.querySelector('meta[name="csrf-token"]');
        var csrfToken = trim(csrfMeta ? csrfMeta.getAttribute('content') : '');
        var data = extraData ? Object.assign({}, extraData) : {};
        var headers = {};

        if (csrfToken) {
            headers['X-CSRF-TOKEN'] = csrfToken;
        }

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

    function requestJson(method, url, extraData) {
        var auth = getApiAuthOptions(extraData);
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
                throw response;
            }

            return response.json();
        });
    }

    function showModal(id) {
        var helpers = getHelpers();
        if (typeof helpers.showModal === 'function') {
            helpers.showModal(id);
            return;
        }

        var modalElement = document.getElementById(id);
        if (!modalElement) {
            return;
        }

        if (window.bootstrap && window.bootstrap.Modal) {
            window.bootstrap.Modal.getOrCreateInstance(modalElement).show();
            return;
        }
    }

    function hideModal(id) {
        var helpers = getHelpers();
        if (typeof helpers.hideModal === 'function') {
            helpers.hideModal(id);
            return;
        }

        var modalElement = document.getElementById(id);
        if (!modalElement) {
            return;
        }

        if (window.bootstrap && window.bootstrap.Modal) {
            window.bootstrap.Modal.getOrCreateInstance(modalElement).hide();
            return;
        }
    }

    function formatDateTime(dateString) {
        var date = new Date(dateString);
        return date.getFullYear() + '-' +
            String(date.getMonth() + 1).padStart(2, '0') + '-' +
            String(date.getDate()).padStart(2, '0') + ' ' +
            String(date.getHours()).padStart(2, '0') + ':' +
            String(date.getMinutes()).padStart(2, '0') + ':' +
            String(date.getSeconds()).padStart(2, '0');
    }

    function appendContactListItem(contacte, alumnoFctName) {
        var ul = document.getElementById('ul_llist');
        if (!ul) {
            return;
        }

        var li = document.createElement('li');
        var wrapper = document.createElement('div');
        wrapper.className = 'message_wrapper';

        var h5 = document.createElement('h5');
        var html =
            '<em class="fa fa-calendar user-profile-icon"></em> ' + formatDateTime(contacte.created_at) +
            ' <em class="fa fa-exclamation"></em>' + (contacte.document || '') +
            ' <em class="fa fa-user user-profile-icon"></em> ' + alumnoFctName;

        if (contacte.comentari) {
            html += '<br/>' + contacte.comentari;
        }

        h5.innerHTML = html;
        wrapper.appendChild(h5);
        li.appendChild(wrapper);
        ul.appendChild(li);
    }

    function loadAlumnat(fctId) {
        requestJson('GET', '/fct/' + fctId + '/alFct')
            .then(function (response) {
                var fctAl = response.data || [];
                var select = document.getElementById('alumnoFct');
                if (!select) {
                    return;
                }

                select.innerHTML = '';

                fctAl.forEach(function (alumne) {
                    var option = document.createElement('option');
                    option.value = alumne.id;
                    option.textContent = alumne.nombre;
                    select.appendChild(option);
                });

                showModal('dialogo_alumno');
            })
            .catch(function (error) {
                console.error('Error en obtenir els alumnes:', error);
            });
    }

    document.addEventListener('DOMContentLoaded', function () {
        var fctIdElement = document.getElementById('fct_id');
        var fctId = trim(fctIdElement ? fctIdElement.textContent : '');

        document.querySelectorAll('input.fa-user').forEach(function (element) {
            element.addEventListener('click', function (event) {
                if (!confirm("Vas a canviar cotutoria d'esta FCT.\n" +
                    "Aquell al que has assignat podrà contactar amb este centre de treball encara que no tinga alumnes assignats.\n" +
                    "El cotutor actual deixarà de vore esta fct sinó te cap alumne assignat")) {
                    event.preventDefault();
                }
            });
        });

        document.querySelectorAll('a.fa-unlink').forEach(function (element) {
            element.addEventListener('click', function (event) {
                if (!confirm("Vas a deslligar la FCT del SAO. L'hauràs de tornar a importar. Estas segur?")) {
                    event.preventDefault();
                }
            });
        });

        document.querySelectorAll('.alumnat').forEach(function (element) {
            element.addEventListener('click', function (event) {
                event.preventDefault();
                if (!fctId) {
                    return;
                }
                loadAlumnat(fctId);
            });
        });

        var formDialogoAlumno = document.getElementById('formDialogo_alumno');
        if (formDialogoAlumno) {
            formDialogoAlumno.addEventListener('submit', function (event) {
                event.preventDefault();

                var alumnoFctSelect = formDialogoAlumno.alumnoFct;
                if (!alumnoFctSelect) {
                    return;
                }

                var alumnoFctId = alumnoFctSelect.value;
                var selectedOption = alumnoFctSelect.options[alumnoFctSelect.selectedIndex];
                var alumnoFctName = selectedOption ? selectedOption.text : '';

                requestJson('POST', '/fct/' + alumnoFctId + '/alFct', {
                    explicacion: formDialogoAlumno.explicacion ? formDialogoAlumno.explicacion.value : ''
                }).then(function (result) {
                    appendContactListItem(result.data, alumnoFctName);
                    hideModal('dialogo_alumno');
                }, function () {
                    console.log('Només es pot un per dia');
                    hideModal('dialogo_alumno');
                });
            });
        }
    });
})();
