'use strict';

var id;
const MODEL = 'comision';

function trim(value) {
    return (value || '').toString().trim();
}

function apiAuthOptions(extraData) {
    var tokenElement = document.querySelector('#_token');
    var legacyToken = trim(tokenElement ? tokenElement.textContent : '');
    var bearerMeta = document.querySelector('meta[name="user-bearer-token"]');
    var bearerToken = trim(bearerMeta ? bearerMeta.getAttribute('content') : '');
    var data = extraData ? Object.assign({}, extraData) : {};
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
        if (!response.ok) {
            throw response;
        }

        return response.text().then(function (text) {
            if (!text) {
                return {};
            }
            try {
                return JSON.parse(text);
            } catch (error) {
                return {};
            }
        });
    });
}

document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.refuse').forEach(function (button) {
        button.addEventListener('click', function (event) {
            event.preventDefault();
            if (window.jQuery) {
                window.jQuery(button).attr('href', '');
            }
            var profile = button.closest('.profile_view');
            id = profile ? profile.getAttribute('id') : '';
        });
    });

    var formDialogo = document.getElementById('formDialogo');
    if (formDialogo) {
        formDialogo.addEventListener('submit', function () {
            formDialogo.setAttribute('action', '/direccion/comision/' + id + '/refuse');
        });
    }

    document.querySelectorAll('.paid').forEach(function (button) {
        button.addEventListener('click', function (event) {
            event.preventDefault();

            var selectedCheckboxes = Array.from(document.querySelectorAll('.user:checked'));
            if (!selectedCheckboxes.length) {
                alert('No hi ha cap casella de selecció seleccionada.');
                return;
            }

            Promise.allSettled(selectedCheckboxes.map(function (checkbox) {
                var url = '/api/comision/' + checkbox.name + '/prePay';
                return apiRequest('PUT', url);
            })).then(function (results) {
                var hasErrors = results.some(function (result) {
                    return result.status !== 'fulfilled';
                });

                if (hasErrors) {
                    alert('S\'ha produit una errada en el processament.');
                    return;
                }

                window.location.href = '/direccion/comision/paid';
            });
        });
    });
});

function getToken() {
    var ppio = document.cookie.indexOf('XSRF-TOKEN=');
    if (ppio === -1) {
        return '';
    }

    ppio += 11;
    var fin = document.cookie.indexOf(';', ppio);
    if (fin === -1) {
        fin = document.cookie.length;
    }

    return document.cookie.substring(ppio, fin);
}
