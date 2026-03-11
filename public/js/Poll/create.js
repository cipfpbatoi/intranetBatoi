'use strict';

(function () {
    function toNumber(value, fallback) {
        var parsed = parseInt(value, 10);
        return Number.isNaN(parsed) ? fallback : parsed;
    }

    function syncTargetVisibility(input) {
        var inputName = input ? input.getAttribute('name') : '';
        if (!inputName) {
            return;
        }

        var target = document.getElementById(inputName);
        if (!target) {
            return;
        }

        target.style.display = String(input.value) === '0' ? '' : 'none';
    }

    document.addEventListener('DOMContentLoaded', function () {
        document.querySelectorAll('.js-range-slider').forEach(function (input) {
            var min = toNumber(input.getAttribute('data-min'), 0);
            var max = toNumber(input.getAttribute('data-max'), 10);
            var from = toNumber(input.getAttribute('data-from'), min);

            input.setAttribute('type', 'range');
            input.setAttribute('min', String(min));
            input.setAttribute('max', String(max));
            input.setAttribute('step', '1');
            input.value = String(from);

            syncTargetVisibility(input);
            input.addEventListener('input', function () {
                syncTargetVisibility(input);
            });
        });
    });
})();
