'use strict';

$(function () {
    $("#contacto_id").change(function () {
        let contacto = $('#contacto_id').val();
        if ($('#instructor_id').val() === '') {
            $('#instructor_id').empty().val(contacto);
        }
    });
    $("button.btn-sm.btn-danger").click(function () {

    })
    $("#fusionar").click(function () {
        let fusionar = [];
        let token = $("#_token").text();
        $('input:checkbox').each(function () {
            if (this.checked)
                fusionar.push($(this).val());
        });
        $.ajax({
            url: "/api/centro/fusionar",
            type: "POST",
            dataType: "json",
            data: {
                api_token: token,
                fusion: fusionar
            }
        }).then(function (res) {
            location.reload();
        }, function (error) {
            showMessage(["Error " + error.status + ": " + error.statusText, "error"], 'error');
        });
    });
});

