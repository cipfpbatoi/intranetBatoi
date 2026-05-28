'use strict';

(function () {
    var id = null;

    document.addEventListener('DOMContentLoaded', function () {
        var formAviso = document.getElementById('formAviso');
        var formDialogo = document.getElementById('formDialogo');
        var formBaja = document.getElementById('formBaja');

        document.addEventListener('click', function (event) {
            var mensaje = event.target.closest('.mensaje');
            if (mensaje) {
                event.preventDefault();
                var profile = mensaje.closest('.profile_view');
                id = profile ? profile.id : null;

                if (window.intranetUiHelpers) {
                    window.intranetUiHelpers.showModal('aviso');
                }
                return;
            }

            var colectivo = event.target.closest('.colectivo');
            if (colectivo) {
                event.preventDefault();
                var profileColectivo = colectivo.closest('.profile_view');
                id = profileColectivo ? profileColectivo.id : null;

                if (window.intranetUiHelpers) {
                    window.intranetUiHelpers.showModal('dialogo');
                }
                return;
            }

            var bajaProfesor = event.target.closest('.baja-profesor');
            if (bajaProfesor) {
                event.preventDefault();
                var profileBaja = bajaProfesor.closest('.profile_view');
                id = profileBaja ? profileBaja.id : null;

                if (window.intranetUiHelpers) {
                    window.intranetUiHelpers.showModal('baja');
                }
            }
        });

        if (formAviso) {
            formAviso.addEventListener('submit', function () {
                var currentId = id || '';
                this.setAttribute('action', '/profesor/' + currentId + '/mensaje');
            });
        }

        if (formDialogo) {
            formDialogo.addEventListener('submit', function () {
                this.setAttribute('action', '/profesor/colectivo');
            });
        }

        if (formBaja) {
            formBaja.addEventListener('submit', function () {
                var currentId = id || '';
                this.setAttribute('action', '/direccion/profesor/' + currentId + '/desactivar');
            });
        }
    });
})();
