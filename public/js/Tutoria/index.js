'use strict';
$(document).ready(function() {
    var table = $('#datatable').DataTable();
    table.order([2, 'dsc']).draw();
});