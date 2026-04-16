$(function () {
    var setFinishLabel = function () {
        $('.buttonFinish').each(function () {
            var $button = $(this);
            if ($button.is('input,button')) {
                $button.val('Enviar').text('Enviar');
                return;
            }

            $button.text('Enviar');
        });
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

    if (window.MutationObserver) {
        var observer = new MutationObserver(function () {
            setFinishLabel();
        });
        observer.observe(document.body, { childList: true, subtree: true });
    }

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
