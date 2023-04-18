'use strict';

$(function () {
    $(".download").on("click", function (event) {
        event.preventDefault();
        $(this).attr("data-toggle", "modal").attr("data-target", "#password").attr("href", "");
    });
    $("#accion").on("change", function (event) {
        event.preventDefault();
        if ($(this).val() === 'a2') {
            $("#decrypt").show();
        } else {
            $("#decrypt").hide();
        }
    });
    $("#password .submit").click(function() {
        localStorage.setItem("cur_modal", '#password');
        event.preventDefault();
        $('#password').modal('hide');
        $("#formPassword" ).submit();
        $(this).attr("data-toggle", "modal").attr("data-target", "#loading").attr("href", "");
    });
    $('.fa-unlink').on("click", function(){
        if (!confirm("Vas a deslligar la FCT del SAO. L'hauràs de tornar a importar. Estas segur?")) {
            event.preventDefault();
        }
    });
});
