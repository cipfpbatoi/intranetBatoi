'use strict';

const MODEL = 'fct';
var id;

document.addEventListener('DOMContentLoaded', function () {
    document.addEventListener('click', function (event) {
        var pdfButton = event.target.closest('.pdf');
        if (!pdfButton) {
            return;
        }

        event.preventDefault();
        pdfButton.setAttribute('data-toggle', 'modal');
        pdfButton.setAttribute('data-target', '#fechas');
        pdfButton.setAttribute('href', '');
        var row = pdfButton.closest('.lineaGrupo') || pdfButton.closest('tr');
        id = row ? row.id : '';
    });

    var formFechas = document.getElementById('formFechas');
    if (formFechas) {
        formFechas.addEventListener('submit', function () {
            this.setAttribute('action', '/' + MODEL + '/' + id + '/pdf');
        });
    }
});
