function apiAuthOptions(extraData) {
    var bearerMeta = document.querySelector('meta[name="user-bearer-token"]');
    var bearerToken = (bearerMeta ? bearerMeta.getAttribute('content') : '') || '';
    var csrfMeta = document.querySelector('meta[name="csrf-token"]');
    var csrfToken = (csrfMeta ? csrfMeta.getAttribute('content') : '') || '';
    var data = extraData ? Object.assign({}, extraData) : {};
    var headers = {};

    bearerToken = bearerToken.trim();
    csrfToken = csrfToken.trim();

    if (csrfToken) {
        headers['X-CSRF-TOKEN'] = csrfToken;
    }
    if (bearerToken) {
        headers.Authorization = 'Bearer ' + bearerToken;
    }

    return { headers: headers, data: data };
}
