'use strict';

$(function () {
    $(".download").on("click", function (event) {
        event.preventDefault();
        $(this).attr("data-toggle", "modal").attr("data-target", "#password").attr("href", "");
    });
    $("#password .submit").click(function() {
        localStorage.setItem("cur_modal", '#password');
        event.preventDefault();
        $('#password').modal('hide');
        $("#formPassword" ).submit();
        $(this).attr("data-toggle", "modal").attr("data-target", "#loading").attr("href", "");
    });
});
