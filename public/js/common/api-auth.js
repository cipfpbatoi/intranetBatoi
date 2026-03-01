function apiAuthOptions(extraData) {
    var bearerToken = $.trim($('meta[name="user-bearer-token"]').attr("content") || "");
    var csrfToken = $.trim($('meta[name="csrf-token"]').attr("content") || "");
    var data = extraData || {};
    var headers = {};

    if (csrfToken) {
        headers["X-CSRF-TOKEN"] = csrfToken;
    }
    if (bearerToken) {
        headers.Authorization = "Bearer " + bearerToken;
    }

    return { headers: headers, data: data };
}
