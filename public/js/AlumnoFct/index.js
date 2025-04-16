'use strict';

$(function () {
    $(".download").on("click", function (event) {
        event.preventDefault();
        $(this).attr("data-toggle", "modal").attr("data-target", "#password").attr("href", "");
    });


    $("#seleccion .submit").click(function() {
        event.preventDefault();
        $("#checkall").prop('checked',false);
        $("#formSeleccion" ).submit();
    });

    $('#mostraDiv').change(function() {
        if($(this).is(':checked')) {
            $('#divSignatura').show();
        } else {
            $('#divSignatura').hide();
        }
    });


    $("#password .submit").click(function() {
        localStorage.setItem("cur_modal", '#password');
        event.preventDefault();
        $('#password').modal('hide');
        $("#formPassword" ).submit();
        $(this).attr("data-toggle", "modal").attr("data-target", "#loading").attr("href", "");
    });
});

