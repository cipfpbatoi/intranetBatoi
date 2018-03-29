'use strict';

$(function () {
    $("#contacto_id").change(function () {
        var contacto = $('#contacto_id').val();
        if ($('#instructor_id').val() === '') {
            $('#instructor_id').empty().val(contacto);
        }
    });
    $("#fusionar").click(function () {
        var fusionar = [];
        var token = $("#_token").text();
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
        //alert ('fusionar'+fusionar);
    });
});

