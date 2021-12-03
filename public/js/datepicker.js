$(document).ready(function () {
        /*$.fn.datepicker.dates['ca'] = {
            days: ["diumenge", "dilluns", "dimarts", "dimecres", "dijous", "divendres", "dissabte"],
            daysShort: ["dg.",  "dl.", "dt.", "dc.", "dj.", "dv.", "ds."],
            daysMin: ["dg", "dl", "dt", "dc", "dj", "dv", "ds"],
            months: ["gener", "febrer", "març", "abril", "maig", "juny", "juliol", "agost", "setembre", "octubre", "novembre", "desembre"],
            monthsShort: ["gen.", "febr.", "març", "abr.", "maig", "juny", "jul.", "ag.", "set.", "oct.", "nov.", "des."],
            today: "Avui",
            monthsTitle: "Mesos",
            clear: "Esborra",
            weekStart: 1,
            format: "dd/mm/yyyy"
        };*/
        $('input[type=text].datetime').datetimepicker({
            sideBySide: true,
            locale: 'es',
            format: 'DD-MM-YYYY LT',
            stepping: 15,
        });
        $('input[type=text].time').datetimepicker({
            sideBySide: true,
            locale: 'es',
            format: 'HH:mm',
            stepping: 15,
        });
        $('input[type=text].date').datetimepicker({
            sideBySide: true,
            format: 'DD-MM-YYYY',
            locale:  moment.locale('es'),
        });
    
});