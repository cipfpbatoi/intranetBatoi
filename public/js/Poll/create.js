'use strict';

(function () {
    document.addEventListener('DOMContentLoaded', function () {
        var jq = window.jQuery || window.$;
        if (!jq || typeof jq.fn.ionRangeSlider !== 'function') {
            return;
        }

        jq('.js-range-slider').ionRangeSlider({
            skin: 'flat',
            grid: 'true',
            onChange: function (data) {
                var inputName = data && data.input ? data.input.attr('name') : null;
                if (!inputName) {
                    return;
                }

                var target = document.getElementById(inputName);
                if (!target) {
                    return;
                }

                target.style.display = data.from === 0 ? '' : 'none';
            }
        });
    });
})();
