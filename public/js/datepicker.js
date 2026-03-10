$(document).ready(function () {
        if (!$.fn.datetimepicker) {
            console.warn('Bootstrap datetimepicker no està disponible: datepicker.js no s’inicialitza.');
            return;
        }
        if (typeof moment === 'undefined') {
            console.warn('Moment.js no està disponible: datepicker.js no s’inicialitza.');
            return;
        }

        var pageLocale = (($('meta[name="app-locale"]').attr('content') || $('html').attr('lang') || 'es').toLowerCase()).split('-')[0];
        var pickerLocale = pageLocale === 'en' ? 'en' : (pageLocale === 'ca' ? 'ca' : 'es');
        var dateFormat = pageLocale === 'en' ? 'MM/DD/YYYY' : 'DD/MM/YYYY';
        var dateTimeFormat = pageLocale === 'en' ? 'MM/DD/YYYY h:mm A' : 'DD/MM/YYYY HH:mm';

        if (typeof moment.locale === 'function') {
            moment.locale(pickerLocale);
        }

        $('input[type="text"].datetime').datetimepicker({
            sideBySide: true,
            locale: pickerLocale,
            format: dateTimeFormat,
            stepping: 15,
        });
        $('input[type="text"].time').datetimepicker({
            sideBySide: true,
            locale: pickerLocale,
            format: 'HH:mm',
            stepping: 15,
        });
        $('input[type="text"].date').datetimepicker({
            sideBySide: true,
            format: dateFormat,
            locale: pickerLocale,
        });
});
