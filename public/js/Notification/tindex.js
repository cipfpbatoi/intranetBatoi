'use strict';

(function () {
    function byId(id) {
        return document.getElementById(id);
    }

    function trim(value) {
        return (value || '').toString().trim();
    }

    function getLegacyToken() {
        var tokenElement = byId('_token');
        return tokenElement ? trim(tokenElement.textContent) : '';
    }

    function getBearerToken() {
        var meta = document.querySelector('meta[name="user-bearer-token"]');
        return meta ? trim(meta.getAttribute('content')) : '';
    }

    function apiAuthOptions(extraData) {
        var data = extraData ? Object.assign({}, extraData) : {};
        var headers = {};
        var bearerToken = getBearerToken();
        var legacyToken = getLegacyToken();

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

    function deleteNotification(id) {
        var auth = apiAuthOptions();
        return fetch(withQueryParams('/notification/' + id + '/delete', auth.data), {
            method: 'GET',
            headers: auth.headers,
            credentials: 'same-origin'
        }).then(function (response) {
            if (!response.ok) {
                throw new Error('HTTP ' + response.status);
            }
            return response.text();
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.del-notif').forEach(function (button) {
            button.addEventListener('click', function () {
                var item = button.closest('li');
                var id = item ? item.id : '';
                if (!id) {
                    return;
                }

                deleteNotification(id)
                    .then(function (result) {
                        window.console.log(result);
                    })
                    .catch(function (error) {
                        window.console.log(error);
                    });
            });
        });
    });
})();
