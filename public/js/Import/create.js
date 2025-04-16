$(function () {
    $(".submit").click(function() {
        $(this).attr("data-toggle", "modal").attr("data-target", "#loading").attr("href", "");
    });
});