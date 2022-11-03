'use strict';

$(function () {
    $(".download").on("click", function (event) {
        event.preventDefault();
        $(this).attr("data-toggle", "modal").attr("data-target", "#password").attr("href", "");
        $("#formPassword").attr("action", "/sao/download");
    });
    $(".sync").on("click", function (event) {
        event.preventDefault();
        $(this).attr("data-toggle", "modal").attr("data-target", "#password").attr("href", "");
        $("#formPassword").attr("action", "/sao/sync");
    });
    $(".check").on("click", function (event) {
        event.preventDefault();
        $(this).attr("data-toggle", "modal").attr("data-target", "#password").attr("href", "");
        $("#formPassword").attr("action", "/sao/check");
    });
    $("#password .submit").click(function() {
        event.preventDefault();
        $('#password').modal('hide');
        $("#formPassword" ).submit();
    });
});
