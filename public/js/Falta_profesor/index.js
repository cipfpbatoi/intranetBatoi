
const COLUMNS=[
            {data: 'departamento'},
            {data: 'nombre'},
            {data: 'codigo_postal'},
            {data: 'email'},
            {data: 'emailItaca'},
        ];

$(function () {
    var token = $("#_token").text();
    $('#datatable').DataTable({
        rowId: 'dni',
        language: {
            url: '/json/cattable.json'
        },
        ajax : {
            method: "GET",
            url: '/api/ficha',
            data: { api_token: token},
        },
        deferRender: true,
        dataSrc: 'data',
        columns: COLUMNS,
    });
    $('#codigo').focus();
});