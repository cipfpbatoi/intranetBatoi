'use strict';

(function () {
    function ensureScaleStyles() {
        if (document.getElementById('poll-range-scale-style')) {
            return;
        }

        var style = document.createElement('style');
        style.id = 'poll-range-scale-style';
        style.textContent = '.poll-range-scale{display:flex;justify-content:space-between;font-size:12px;color:#666;margin-top:4px;padding:0 2px}.poll-range-scale span{min-width:12px;text-align:center}';
        document.head.appendChild(style);
    }

    function toNumber(value, fallback) {
        var parsed = parseInt(value, 10);
        return Number.isNaN(parsed) ? fallback : parsed;
    }

    function addScaleRuler(input, min, max) {
        if (!input || input.nextElementSibling && input.nextElementSibling.classList.contains('poll-range-scale')) {
            return;
        }

        var ruler = document.createElement('div');
        ruler.className = 'poll-range-scale';

        for (var i = min; i <= max; i += 1) {
            var tick = document.createElement('span');
            tick.textContent = String(i);
            ruler.appendChild(tick);
        }

        input.parentNode.insertBefore(ruler, input.nextSibling);
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
        ensureScaleStyles();

        document.querySelectorAll('.js-range-slider').forEach(function (input) {
            var min = toNumber(input.getAttribute('data-min'), 0);
            var max = toNumber(input.getAttribute('data-max'), 10);
            var from = toNumber(input.getAttribute('data-from'), min);

            input.setAttribute('type', 'range');
            input.setAttribute('min', String(min));
            input.setAttribute('max', String(max));
            input.setAttribute('step', '1');
            input.value = String(from);
            addScaleRuler(input, min, max);

            syncTargetVisibility(input);
            input.addEventListener('input', function () {
                syncTargetVisibility(input);
            });
        });
    });
})();
