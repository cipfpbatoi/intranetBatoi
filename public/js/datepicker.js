$(document).ready(function () {
        
        $('input[type=text].datetime').datetimepicker({
            sideBySide: true,
            locale: 'es',
            format: 'DD-MM-YYYY LT',
            stepping: 15,
        });
        $('input[type=text].time').datetimepicker({
            sideBySide: true,
            locale: 'es',
            format: 'hh:mm',
            stepping: 15,
        });
        $('input[type=text].date').datetimepicker({
            sideBySide: true,
            locale: 'es',
            format: 'DD-MM-YYYY',
        });
    
});