(function (global) {
    function initDatepickers() {
        var $jq = global.jQuery;
        var dateInputs = document.querySelectorAll('input[type="text"].date');
        var timeInputs = document.querySelectorAll('input[type="text"].time');
        var dateTimeInputs = document.querySelectorAll('input[type="text"].datetime');
        var hasPickerTargets = dateInputs.length || timeInputs.length || dateTimeInputs.length;

        function applyNativeFallback() {
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

        if (!hasPickerTargets) {
            return;
        }

        if (typeof $jq !== 'function') {
            applyNativeFallback();
            return;
        }

        if (!$jq.fn || !$jq.fn.datetimepicker) {
            applyNativeFallback();
            return;
        }

        if (typeof global.moment === 'undefined') {
            applyNativeFallback();
            return;
        }

        var pageLocale = (($jq('meta[name="app-locale"]').attr('content') || $jq('html').attr('lang') || 'es').toLowerCase()).split('-')[0];
        var pickerLocale = pageLocale === 'en' ? 'en' : (pageLocale === 'ca' ? 'ca' : 'es');
        var dateFormat = pageLocale === 'en' ? 'MM/DD/YYYY' : 'DD/MM/YYYY';
        var dateTimeFormat = pageLocale === 'en' ? 'MM/DD/YYYY h:mm A' : 'DD/MM/YYYY HH:mm';

        if (typeof global.moment.locale === 'function') {
            global.moment.locale(pickerLocale);
        }

        $jq(dateTimeInputs).datetimepicker({
            sideBySide: true,
            locale: pickerLocale,
            format: dateTimeFormat,
            stepping: 15,
        });

        $jq(timeInputs).datetimepicker({
            sideBySide: true,
            locale: pickerLocale,
            format: 'HH:mm',
            stepping: 15,
        });

        $jq(dateInputs).datetimepicker({
            sideBySide: true,
            format: dateFormat,
            locale: pickerLocale,
        });
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initDatepickers);
        return;
    }

    initDatepickers();
})(window);
