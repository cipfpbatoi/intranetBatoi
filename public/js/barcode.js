var $ = window.jQuery || window.$;
$(function () {
    //  Barcode
    $('#datatable').on('click', 'a.QR', function (event) {
        let url = $(this).prop('href') + '/';
        event.preventDefault();
        var posicion = window.prompt("Introdueix posició de la primera etiqueta", 1);
        url += posicion;
        $(location).attr('href', url);
    });
});
