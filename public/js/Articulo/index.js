'use strict';


var selecionado = null;
var options = {};


    // FUncionalidades de los botones
    var editar =  `<a href="#" class="edit">
                    <i class="fa fa-pencil" title="Editar"></i>
                </a>`;
    var token = $("#_token").text();


$(function () {


    // Botón VER
    $('a.imgButton').on('click', function (event) {
        event.preventDefault();
        var idArticulo = $(this).parent().parent().siblings().first().text();
        $.ajax({
            context:this,
            method: "GET",
            url: "/api/espacio",
            data: {api_token: token},
            dataType: "json",
        }).then(function (result) {
            $(result.data).each(function (i, item) {
                options[item.aula] = item.descripcion ;
            });
            cargaMateriales(this,idArticulo);
        });
    })
    $('#dialogo').on('click', 'a.edit', editRow);
})

function cargaMateriales(entorno,idArticulo){
    $(entorno).attr("data-toggle","modal").attr("data-target", "#dialogo").attr("href","");
    $.ajax({
        context: entorno,
        method: "GET",
        url: "/api/articulo/" + idArticulo +"/materiales",
        data: { api_token: token},
        dataType: "json",
    }).then(function (result) {
        var html = '<table id="datamateriales" name="material" class="table table-striped"><thead><tr><th>Id</th><th>Numero Serie</th><th>Espai</th>';
        var descripcion = '';
        html += '<th>Operacions</th></tr></thead><tbody>';
        $(result.data).each(function (  i, item) {
            html += '<tr id="'+item.id+'"><td>'+item.id+'</span></td>'+
                '<td><span class="input" name="nserieprov">'+ item.nserieprov+
                '</span></td><td><span class="objselect" name="espacio">'+item.espacio+
                '</td><td><span class="botones">'+editar;
            descripcion = item.descripcion;
        });
        html += '</tbody></table>';
        $("#dialogo .modal-title").html("Materials del Article <span id='idLote'>"+descripcion+'</span>');
        $("#dialogo .modal-body").html(html);
        $("#dialogo .modal-footer").find("button[type=submit]").hide();
        $("#dialogo .modal-footer").find("button[type=button]").text("Cerrar").one("click", function() {
            $("#dialogo").modal("hide");
        });
        $("#dialogo").modal("show");
    });
}


function htmlDialog(dlgControls) {
    let html=`<form class="form-horizontal form-label-left">
        `;
    for (let control of dlgControls) {
        html+=`<div class="form-group">
            `;
        html+=`<label class="control-label col-md-3 col-sm-3 col-xs-12" for="${control.id.toLowerCase()}">${control.id}</label>
                `;
        html+=`<div class="col-md-6 col-sm-6 col-xs-12">`;
        switch (control.type) {
            case 'text':
                html+=`<input type="text" id="${control.id.toLowerCase()}" `;
                if (control.val!=undefined)
                    html+=`value="${control.val}" `;
                html+=`class="form-control" />
                    `;
                break;
            case 'input':
                html+=`<input type="number" id="${control.id.toLowerCase()}" `;
                if (control.val!=undefined)
                    html+=`value="${control.val}" `;
                html+=`class="form-control" />
                    `;
                break;
            case 'select':
                html+=`<select id="${control.id.toLowerCase()}" class="form-control"></select>
                    `;
                break;
            case 'textarea':
                html+=`<textarea id="${control.id.toLowerCase()}" class="form-control"></textarea>
                    `;
                break;
            case 'span':
                html+=`<span id="${control.id.toLowerCase()}" class="form-control">`;
                if (control.val!=undefined)
                    html+=control.val;
                html+=`</span>
                    `;
                break;
        }
        html+=`</div>
        </div>
        `;
    }
    html+=`</form>`;
    return html;
}
