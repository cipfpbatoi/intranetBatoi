(function (global) {
    'use strict';

    function trim(value) {
        return (value || '').toString().trim();
    }

    function getMetaContent(name) {
        var meta = document.querySelector('meta[name="' + name + '"]');
        return trim(meta ? meta.getAttribute('content') : '');
    }

    function getLegacyToken() {
        var legacyToken = '';

        document.querySelectorAll('#_token').forEach(function (tokenElem) {
            if (legacyToken) {
                return;
            }

            var candidate = trim(tokenElem.textContent || tokenElem.value || '');
            if (candidate) {
                legacyToken = candidate;
            }
        });

        return legacyToken;
    }

    function apiAuthOptions(extraData) {
        var bearerToken = getMetaContent('user-bearer-token');
        var csrfToken = getMetaContent('csrf-token');
        var legacyToken = getLegacyToken();
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

    function fetchJson(url, options) {
        return fetch(url, options).then(function (response) {
            if (!response.ok) {
                throw new Error('HTTP ' + response.status);
            }

            return response.json();
        });
    }

    function apiGet(url, extraData) {
        var auth = apiAuthOptions(extraData);
        return fetchJson(withQueryParams(url, auth.data), {
            method: 'GET',
            headers: auth.headers,
            credentials: 'same-origin'
        });
    }

    global.intranetApiAuth = {
        apiAuthOptions: apiAuthOptions,
        withQueryParams: withQueryParams,
        fetchJson: fetchJson,
        apiGet: apiGet
    };

    global.apiAuthOptions = apiAuthOptions;
})(window);
