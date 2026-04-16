$(function () {
    var setFinishLabel = function () {
        $('.buttonFinish').text('Enviar').val('Enviar');
    };

    setFinishLabel();
    var relabelTries = 0;
    var relabelInterval = setInterval(function () {
        setFinishLabel();
        relabelTries++;
        if ($('.buttonFinish').length > 0 || relabelTries > 20) {
            clearInterval(relabelInterval);
        }
    }, 100);

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
