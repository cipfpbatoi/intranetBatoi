'use strict';

const MODEL = 'incidencia';
var id;

document.addEventListener('DOMContentLoaded', function () {
    document.addEventListener('click', function (event) {
        var refuseButton = event.target.closest('.refuse');
        if (refuseButton) {
            event.preventDefault();
            refuseButton.setAttribute('data-toggle', 'modal');
            refuseButton.setAttribute('data-target', '#dialogo');
            refuseButton.setAttribute('href', '');
            var refuseCard = refuseButton.closest('.profile_view');
            id = refuseCard ? refuseCard.id : '';
            return;
        }

        var resolveButton = event.target.closest('.resolve');
        if (resolveButton) {
            event.preventDefault();
            resolveButton.setAttribute('data-toggle', 'modal');
            resolveButton.setAttribute('data-target', '#aviso');
            resolveButton.setAttribute('href', '');
            var resolveCard = resolveButton.closest('.profile_view');
            id = resolveCard ? resolveCard.id : '';
        }
    });

    var formDialogo = document.getElementById('formDialogo');
    if (formDialogo) {
        formDialogo.addEventListener('submit', function () {
            this.setAttribute('action', MODEL + '/' + id + '/refuse');
        });
    }

    var formAviso = document.getElementById('formAviso');
    if (formAviso) {
        formAviso.addEventListener('submit', function () {
            this.setAttribute('action', '/mantenimiento/' + MODEL + '/' + id + '/resolve');
        });
    }

    var explicacion = document.getElementById('explicacion');
    if (explicacion) {
        explicacion.focus();
    }
});
 
