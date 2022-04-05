'use strict';

const PROCEDENCIAS=['Desconocido', 'Dotación', 'Compra', 'Donación'];
const ESTADOS=[ 'BUIDA','ALTA', 'INVENTARIANT','FINALITZADA'];
const MANTENIMIENTO=7;
var selecionado = null;
var options = {};


    // FUncionalidades de los botones
    var autorizado=(!($('#rol').text()%MANTENIMIENTO));
    var contenido = `<a href="#" class="ver">
                    <i class="fa fa-eye" title="Ver"></i>                
                </a>`;
    var borrar = `<a href="#" class="delete">
                    <i class="fa fa-trash" title="Borrar"></i>
                </a>`;
    var editar =  `<a href="#" class="edit">
                    <i class="fa fa-pencil" title="Editar"></i>
                </a>`;
    var operaciones = borrar + editar;
    var capturar = `<a href="#" class="capture">
                    <i class="fa fa-flag" title="Capturar des d'inventari"></i>
                </a>`;
    var inventariable = `<a href="#" class="inventary">
                    <i class="fa fa-cubes" title="Inventariar"></i>                
                </a>`;
    var imprimir = `<a href="#" class="QR">
                    <i class="fa fa-barcode" title="Codi QR"></i>                
                </a>`;
    var token = $("#_token").text();
    var articulos = cargaArticulos();
    $("#datatable").DataTable( {
        ajax : {
            method: "GET",
            url: '/api/lote',
            data: { api_token: token},
        },
        deferRender : true,
        dataSrc : 'data',
        rowId : 'registre',
        order: [[ 4, "desc" ]],
        columnDefs: [
            {className: "estado", "targets": [ 4 ]} ,
            {className: "operaciones", "targets": [ 6 ]} ,
        ],
        columns: [
            { data:'registre'},
            { data:'proveedor'},
            { data:'factura'},
            {
                data: null, render: function (data) {
                    if (data.procedencia)
                        return PROCEDENCIAS[data.procedencia];
                    else return 'Desconocido';
                    },
            },
            {
                data: null, render: function (data) {
                        return ESTADOS[data.estado];
                    },
            },
            { data:'fechaAlta'},
            { data: null, render: function (data){
                return  (data.estado == 1) ? contenido+operaciones+inventariable:
                        (data.estado == 2) ? contenido+editar+
                            `<a href="/direccion/lote/`+data.registre+`/print" class="QR">
                                <i class="fa fa-barcode" title="Codi QR"></i>                
                            </a>`:
                        (data.estado == 0) ? contenido+operaciones+capturar:contenido+operaciones;
                },
            },
        ],
//        rowCallback: function ( row, data ) {
//            // Set the checked state of the checkbox in the table
//            $('input.editor-active', row).prop( 'checked', data.fechaultimoinventario != null );
//        },
        language: {
            url: "/json/cattable.json"
        }
    });

$(function () {

    // create

    $(".txtButton").on("click", function () {
        event.preventDefault();
        $("#create").modal("show");
    });


    // Botón VER
    $('#datatable').on('click', 'a.ver', function (event) {
        event.preventDefault();
        var idLote = $(this).parent().siblings().first().text();
        var estado = $(this).parent().siblings(".estado").text();
        if (estado == 'ALTA' || estado == 'BUIDA') {
            estado = true;
        }
        else {
            estado = false;
        }
        cargaArticulosLote(this,idLote,estado);
    })

    if (autorizado) {
        // DATALOTE
        // Botón DELETE
        $('#datatable').on('click', 'a.delete', function (event) {
            let info="\n";
            let titles=$(this).parents('table').find('thead').find('th');
            $(this).parent().siblings().each(function(i, item) {
                if (item.innerHTML.trim().length>0) {
                    info+=` - ${titles.eq(i).text().trim()}: ${item.innerHTML}\n`;
                }
            })
            if (confirm('Vas a borrar el elemento:'+info)) {
                $.ajax({
                    context: this,
                    method: "DELETE",
                    url: "/api/lote/" + $(this).parent().siblings().first().text(),
                    data: { api_token: token},
                    dataType: "json",
                }).then(function (result) {
                    if (result.success == true ) {
                        $(this).parent().parent().addClass('danger');
                    }
                });
            } else {
                event.preventDefault();
            }
        })

        
        $('#datatable').on('click', 'a.capture', function (event) {
            var url = '/direccion/lote/'+$(this).parent().siblings().first().text()+'/capture';
            $(location).attr('href',url);
        })

        // Botón EDIT
        $('#datatable').on('click', 'a.edit', function (event) {
            event.preventDefault();
            $(this).attr("data-toggle","modal").attr("data-target", "#dialogo").attr("href","");
            var $registre = $(this).parent().siblings().first();
            var $proveedor = $(this).parent().siblings().eq(1);
            var $factura = $(this).parent().siblings().eq(2);
            var $origen = $(this).parent().siblings().eq(3);
            var $fechaAlta = $(this).parent().siblings().eq(5);
            let dlgControls=[
                {id: "Registre", type: "text", val:$registre.text()},
                {id: "Factura", type: "text", val:$factura.text()},
                {id: "Proveedor", type: "text", val: $proveedor.text()},
                {id: "Origen", type: "select"},
                {id: "FechaAlta", type: "text", val: $fechaAlta.text()},
            ];
            $("#dialogo .modal-title").text("Editar Factura "+$registre.text());
            $("#dialogo .modal-body").html(htmlDialog(dlgControls));
            $("#dialogo .modal-footer").find("button[type=button]").text("Cancelar");
            $("#dialogo .modal-footer").find("button[type=submit]").show().one("click", function() {
                $.ajax({
                    method: "PUT",
                    url: "/api/lote/"+$registre.text(),
                    data: {
                        registre: $("#registre").val(),
                        factura: $("#factura").val(),
                        proveedor: $("#proveedor").val(),
                        procedencia: $("#origen").val(),
                        fechaAlta: $("#fechaalta").val(),
                        api_token: token,
                    },
                }).then(function (res) {
                    $registre.text($("#registre").val());
                    $proveedor.text($("#proveedor").val());
                    $factura.text($("#factura").val());
                    $origen.text(PROCEDENCIAS[$("#origen").val()]);
                    $fechaAlta.text($("#fechaalta").val());
                    console.log(res)
                }, function (res) {
                    console.log(res)
                });
                $("#dialogo").modal("hide");
            });
            $("#origen").append("<option value='0'"+ ($origen.text() == 'No se'?'" selected>':'">') + "Desconocido</option>");
            $("#origen").append("<option value='1'"+ ($origen.text() == 'Dotación'?'" selected>':'">') + "Dotación </option>");
            $("#origen").append("<option value='2'"+ ($origen.text() == 'Compra'?'" selected>':'">') + "Compra </option>");
            $("#origen").append("<option value='3'"+ ($origen.text() == 'Donación'?'" selected>':'">') + "Donación </option>")

        });
        // INVENTARIAR
        $('#datatable').on('click', 'a.inventary', function (event) {
            var idLote = $(this).parent().siblings().first().text();
            var texto = 'Vas a inventariar el lot amb registre '+idLote+', amb els següents articles.\n';
            var jefes = [];
            $.ajax({
                context:this,
                method: "GET",
                url: "/api/lote/" + idLote+"/articulos",
                data: { api_token: token},
                dataType: "json",
            }).then(function (result) {
                $(result.data).each(function (  i, item) {
                    texto += item.unidades + ' de '+ item.descripcion+'\n';
                });
                $.ajax({
                    method: "GET",
                    url: "/api/departamento",
                    data: { api_token: token},
                    dataType: "json",
                }).then( function(result){
                    if (result.success == true ){
                        $(result.data.data).each(function (  i, item) {
                            jefes[item.id] = item.name;
                        });
                    }
                    let advise = prompt(texto)
                    if (confirm(texto)) {
                        $.ajax({
                            context: this,
                            method: "PUT",
                            url: "/api/lote/" + idLote +'/articulos',
                            data: { api_token: token,
                                inventariar: true},
                            dataType: "json",
                        }).then(function (result) {
                            if (result.success == true ){
                                $(this).parent().parent().addClass('danger');
                                $(this).parent().siblings('.estado').text("INVENTARIANT");
                                $(this).parent().html(contenido);
                            }
                        }).fail(error=>console.log(error)) ;
                    }
                });

            })
        })

        // DIALOGO ARTICLES
        $("#dialogo").on('change','#articulo_id',function () {
            if ($('#articulo_id').val() === 'new') {
                $('#descripcion').removeClass('hidden');
            }
            else {
                $('#descripcion').addClass('hidden');
            }
        });
        $('#dialogo').on('click', 'a.ver', function (event) {
            event.preventDefault();
            $("#dialogo").modal("hide");
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
        $('#dialogo').on('click', 'a.new', function (event,idLote) {
            event.preventDefault();
            $.ajax({
                context: this,
                method: "POST",
                url: "/api/articuloLote",
                data: { api_token: token,
                    lote_id: $("#idLote").text(),
                    articulo_id: $("#articulo_id").val(),
                    descripcion: $("#descripcion").val(),
                    marca: $("#marca").val(),
                    modelo: $("#modelo").val(),
                    unidades: $("#unidades").val(),
                },
                dataType: "json",
            }).then(function (result) {
                if (result.success == true ) {
                    cargaArticulosLote(this,$("#idLote").text(),true)
                }
            }).fail(error=>console.log(error)) ;
        })
        // Botón DELETE article
        $('#dialogo').on('click', 'a.delete', function (event) {
            let info="\n";
            let titles=$(this).parents('table').find('thead').find('th');
            $(this).parent().parent().siblings().each(function(i, item) {
                if (item.innerHTML.trim().length>0) {
                    info+=` - ${titles.eq(i).text().trim()}: ${item.innerHTML}\n`;
                }
            })
            if (confirm('Vas a borrar el elemento:'+info)) {
                $.ajax({
                    context: this,
                    method: "DELETE",
                    url: "/api/articuloLote/" + $(this).parent().parent().siblings().first().text(),
                    data: { api_token: token},
                    dataType: "json",
                }).then(function (result) {
                    if (result.success == true ) {
                        cargaArticulosLote(this,$("#idLote").text(),true)
                    }
                });
            } else {
                event.preventDefault();
            }
        })
        $('#dialogo').on('click', 'a.edit', editRow);
        $('#materiales').on('click', 'a.edit', editRow);

    }
})

function cargaArticulosLote(entorno,idLote,estado){
    $(entorno).attr("data-toggle","modal").attr("data-target", "#dialogo").attr("href","");
    $.ajax({
        context: entorno,
        method: "GET",
        url: "/api/lote/" + idLote+"/articulos",
        data: { api_token: token},
        dataType: "json",
    }).then(function (result) {
        var html = '<table id="dataarticle" name="articuloLote" class="table table-striped"><thead><tr><th>Id</th><th>Article</th><th>Marca</th><th>Mòdel</th><th>Unitats</th>';
        html += '<th>Operacions</th></tr></thead><tbody>';
        $(result.data).each(function (  i, item) {
            html += '<tr id="'+item.id+'"><td>'+item.id+'</span></td><td><span class="none" name="descripcion">'+item.descripcion+
                '</span></td><td><span class="input" name="marca">'+ item.marca+
                '</span></td><td><span class="input" name="modelo">'+item.modelo+
                '</td><td class="unidades"><span class="input" name="unidades">'+item.unidades+'</span></td><td><span class="botones">'+contenido;
            if (estado) html += operaciones;
        });
        if (estado) {
            html += '</span></td></tr><tr><td></td><td>' + articulos +
                '<br/><input type="text" id="descripcion" name="descripcion" class="hidden"></td>'+
                '<td><input type="text" id="marca" name="marca" /></td>'+
                '<td><input type="text" id="modelo" name="modelo" /></td>'+
                '<td><input type=number" id="unidades" name="unidades" /></td><td><a class="btn btn-info new">Afegir Article</a></td></tr>';
        }
        html += '</tbody></table>';
        $("#dialogo .modal-title").html("Articles del Lot <span id='idLote'>"+result.lote+'</span>');
        $("#dialogo .modal-body").html(html);
        $("#dialogo .modal-footer").find("button[type=submit]").hide();
        $("#dialogo .modal-footer").find("button[type=button]").text("Cerrar");
    });
}

function cargaArticulos() {
    $.ajax({
        method: "GET",
        url: "/api/articulo",
        data: {api_token: token},
        dataType: "json",
    }).then(function (result) {
        articulos = '<select id="articulo_id" name="articulo_id" ><option value="new">Article Nou</option>';
        $(result.data).each(function (i, item) {
            if (i == 0) {
                articulos += "<option value='" + item.id + "' selected>" + item.descripcion + "</option>";
            } else {
                articulos += "<option value='" + item.id + "'>" + item.descripcion + "</option>";
            }
        });
    });
}

function cargaMateriales(entorno,idArticulo){
    $(entorno).attr("data-toggle","modal").attr("data-target", "#materiales").attr("href","");
    $.ajax({
        context: entorno,
        method: "GET",
        url: "/api/articuloLote/" + idArticulo +"/materiales",
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
        $("#materiales .modal-title").html("Materials del Article <span id='idLote'>"+descripcion+'</span>');
        $("#materiales .modal-body").html(html);
        $("#materiales .modal-footer").find("button[type=submit]").hide();
        $("#materiales .modal-footer").find("button[type=button]").text("Cerrar").one("click", function() {
            $("#dialogo").modal("show");
            $("#materiales").modal("hide");
        });
        $("#materiales").modal("show");
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
