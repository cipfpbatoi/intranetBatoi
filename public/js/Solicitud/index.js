'use strict';

const MODEL = 'solicitud';
var id;

document.addEventListener('DOMContentLoaded', function () {
    document.addEventListener('click', function (event) {
        var resolveButton = event.target.closest('#datatable .resolve');
        if (!resolveButton) {
            return;
        }

        event.preventDefault();
        resolveButton.setAttribute('data-toggle', 'modal');
        resolveButton.setAttribute('data-target', '#resolve');
        resolveButton.setAttribute('href', '');
        var row = resolveButton.closest('.lineaGrupo') || resolveButton.closest('tr');
        id = row ? row.id : '';
    });

    var formResolve = document.getElementById('formResolve');
    if (formResolve) {
        formResolve.addEventListener('submit', function () {
            this.setAttribute('action', '/' + MODEL + '/' + id + '/resolve');
        });
    }

    var explicacion = document.getElementById('explicacion');
    if (explicacion) {
        explicacion.focus();
    }
});
