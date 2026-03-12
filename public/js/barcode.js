'use strict';

(function () {
    document.addEventListener('DOMContentLoaded', function () {
        var datatable = document.getElementById('datatable');
        if (!datatable) {
            return;
        }

        datatable.addEventListener('click', function (event) {
            var link = event.target.closest('a.QR');
            if (!link) {
                return;
            }

            event.preventDefault();
            var posicion = window.prompt('Introdueix posició de la primera etiqueta', 1);
            var url = (link.getAttribute('href') || '') + '/' + posicion;
            window.location.href = url;
        });
    });
})();
