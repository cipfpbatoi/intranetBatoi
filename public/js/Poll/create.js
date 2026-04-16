$(function () {
    var renameControl = function ($control) {
        if ($control.is('input,button')) {
            $control.val('Enviar').text('Enviar');
            return;
        }

        $control.text('Enviar');
    };

    var setFinishLabel = function () {
        var $wizard = $('#wizard');
        if ($wizard.length === 0) {
            return;
        }

        $wizard.find('.buttonFinish, .sw-btn-finish, [data-sw-btn="finish"]').each(function () {
            renameControl($(this));
        });

        $wizard.find('a, button, input[type="button"], input[type="submit"]').each(function () {
            var $control = $(this);
            var text = $.trim($control.text());
            var value = $.trim($control.val() || '');
            var title = $.trim($control.attr('title') || '');
            if (/finish/i.test(text) || /finish/i.test(value) || /finish/i.test(title)) {
                renameControl($control);
            }
        });
    };

    setFinishLabel();
    var relabelTries = 0;
    var relabelInterval = setInterval(function () {
        setFinishLabel();
        relabelTries++;
        if ($('.buttonFinish, .sw-btn-finish, [data-sw-btn="finish"]').length > 0 || relabelTries > 50) {
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
