'use strict';
// S'executa síncronament quan el script defer es carrega (abans de DOMContentLoaded
// i dels callbacks $.ready). Estableix l'ordre per defecte abans que DataTables
// s'inicialitze, de manera que qualsevol inicialitzador (custom.js, grid.js) el recull.
(function () {
    var table = document.getElementById('datatable');
    if (table && !table.getAttribute('data-order')) {
        table.setAttribute('data-order', '[[2,"desc"]]');
    }
}());
