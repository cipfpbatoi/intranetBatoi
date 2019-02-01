$(function () {
    $(".js-range-slider").ionRangeSlider({
        skin: "flat",
        grid: 'true',
        onChange: function (data) {
            id = '#' + data.input.attr('name');
            if (data.from == 0)
                $(id).show();
            else
                $(id).hide();
        }
    });
});

