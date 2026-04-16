$(function () {
    $('.buttonFinish').text('Enviar').val('Enviar');

    $(".js-range-slider").ionRangeSlider({
        skin: "flat",
        grid: 'true',
        onStart: function (data) {
            var id = '#' + data.input.attr('name');
            if (data.from === 0) {
                $(id).show();
            } else {
                $(id).hide();
            }
        },
        onChange: function (data) {
            var id = '#' + data.input.attr('name');
            if (data.from == 0)
                $(id).show();
            else
                $(id).hide();
        }
    });
});
