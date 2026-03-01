function apiAuthOptions(extraData) {
    var legacyToken = $.trim($("#_token").text());
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
    if (legacyToken) {
        data.api_token = legacyToken;
    }

    return { headers: headers, data: data };
}
