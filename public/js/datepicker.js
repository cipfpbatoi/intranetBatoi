(function (global) {
    function getPageLocale($jq) {
        return (($jq('meta[name="app-locale"]').attr('content') || $jq('html').attr('lang') || 'es').toLowerCase()).split('-')[0];
    }

    function getPickerConfig(pageLocale) {
        return {
            pickerLocale: pageLocale === 'en' ? 'en' : (pageLocale === 'ca' ? 'ca' : 'es'),
            dateFormat: pageLocale === 'en' ? 'MM/DD/YYYY' : 'DD/MM/YYYY',
            dateTimeFormat: pageLocale === 'en' ? 'MM/DD/YYYY h:mm A' : 'DD/MM/YYYY HH:mm'
        };
    }

    function applyNativeFallback(dateInputs, timeInputs, dateTimeInputs) {
        dateInputs.forEach(function (input) {
            input.setAttribute('type', 'date');
        });

        timeInputs.forEach(function (input) {
            input.setAttribute('type', 'time');
        });

        dateTimeInputs.forEach(function (input) {
            input.setAttribute('type', 'datetime-local');
        });
    }

    function initDatepickers(root) {
        var scope = root || document;
        var $jq = global.jQuery;
        var dateInputs = scope.querySelectorAll('input[type="text"].date');
        var timeInputs = scope.querySelectorAll('input[type="text"].time');
        var dateTimeInputs = scope.querySelectorAll('input[type="text"].datetime');
        var hasPickerTargets = dateInputs.length || timeInputs.length || dateTimeInputs.length;

        if (!hasPickerTargets) {
            return;
        }

        if (typeof $jq !== 'function') {
            applyNativeFallback(dateInputs, timeInputs, dateTimeInputs);
            return;
        }

        if (!$jq.fn || !$jq.fn.datetimepicker) {
            applyNativeFallback(dateInputs, timeInputs, dateTimeInputs);
            return;
        }

        if (typeof global.moment === 'undefined') {
            applyNativeFallback(dateInputs, timeInputs, dateTimeInputs);
            return;
        }

        var config = getPickerConfig(getPageLocale($jq));

        if (typeof global.moment.locale === 'function') {
            global.moment.locale(config.pickerLocale);
        }

        $jq(dateTimeInputs).datetimepicker({
            sideBySide: true,
            locale: config.pickerLocale,
            format: config.dateTimeFormat,
            stepping: 15,
        });

        $jq(timeInputs).datetimepicker({
            sideBySide: true,
            locale: config.pickerLocale,
            format: 'HH:mm',
            stepping: 15,
        });

        $jq(dateInputs).datetimepicker({
            sideBySide: true,
            format: config.dateFormat,
            locale: config.pickerLocale,
        });
    }

    global.intranetDatepickers = {
        init: initDatepickers
    };

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function () {
            initDatepickers(document);
        });
        return;
    }

    initDatepickers(document);
})(window);
