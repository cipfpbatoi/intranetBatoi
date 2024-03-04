function pintaTablaSeleccion(newOptions,tabla){
    var $el = $(tabla);
    var checked;
    var emptys = 0;
    var checks = 0;
    $el.empty(); // remove old options
    $.each(newOptions, function (key, value) {
        if (value.marked  || value.marked == null){
            checked = ' checked';
            checks += 1;
        }
        else {
            checked = '';
            emptys += 1;
        }
        $el.append($("<tr><td><input type='checkbox' class='elements' name='"+value.id+"'"+checked+"> "+value.texto+"</td></tr>"));
    });
    if (checks > emptys) {
        checked = 'checked';
    } else {
        checked = ' ';
    }
    $el.append($("<tr><td>-------------------------------</td></tr>"))
    $el.append($("<tr><td><div id='divCheckAll'><input type='checkbox' name='checkall' id='checkall' onClick='check_uncheck_checkbox(this.checked);'"+checked+"/>Check All</div></td></tr>"));
}

function check_uncheck_checkbox(isChecked) {
    if(isChecked) {
        $('input.elements').each(function() {
            this.checked = true;
        });
    } else {
        $('input.elements').each(function() {
            this.checked = false;
        });
    }
}