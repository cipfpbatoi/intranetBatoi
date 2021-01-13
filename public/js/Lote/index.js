'use strict';

const PROCEDENCIAS=['Desconocido', 'Dotación', 'Compra', 'Donación'];
const ESTADOS=['Desconocido', 'Ok', 'Reparándose', 'Baja'];
const MANTENIMIENTO=7;
var selecionado = null;


    // FUncionalidades de los botones
    var autorizado=(!($('#rol').text()%MANTENIMIENTO));

     // Código para pintar los botones de la datatable
    var contenido="";
    var contenido1 = "";
    if (autorizado) {
        contenido1 = `<a href="#" class="delete">
                    <i class="fa fa-trash" title="Borrar"></i>
                </a>
                 <a href="#" class="edit">
                    <i class="fa fa-pencil" title="Editar"></i>
                </a>
                <a href="#" class="copy">
                    <i class="fa fa-copy" title="Copiar"></i>                
                </a>
                `;
    }
    contenido = contenido1 +`            
                <a href="#" class="ver">
                    <i class="fa fa-eye" title="Ver"></i>                
                </a>
    `;
    var token = $("#_token").text();
    $("#datalote").DataTable( {
        ajax : {
            method: "GET",
            url: '/api/lote',
            data: { api_token: token},
        },
        deferRender : true,
        dataSrc : 'data',
        rowId : 'id',
        columnDefs: [
            { className: "unidades", "targets": [ 5 ] },
        ],
        columns: [
            { data:'id'},
            { data:'descripcion'},
            {
                data: null, render: function (data) {
                    if (data.procedencia)
                        return PROCEDENCIAS[data.procedencia];
                    else return 'Desconocido';
                },
            },
            { data:'proveedor'},
            { data:'registre'},
            { data:'unidades'},
            { data: null, defaultContent: contenido},
            { data: null ,
              render: function (data, type,row ) {
                    if (data.inventariable)
                        return '<a href="#" class="inventari"><i class="fa fa-check-square-o" title="Inventariable"></i></a>';
                    else
                        return '<a href="#" class="noinventari"><i class="fa fa-square-o" title="No inventariable"></i></a>';
                  return data;
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


    // Botón VER
    $('#datalote').on('click', 'a.ver', function (event) {
        event.preventDefault();
        $(this).attr("data-toggle","modal").attr("data-target", "#dialogo").attr("href","");
        var idLote = $(this).parent().siblings().first().text();
        $.ajax({
            context: this,
            method: "GET",
            url: "/api/lote/" + idLote+"/articulos",
            data: { api_token: token},
            dataType: "json",
        }).then(function (result) {
            var html = '<table id="dataarticle" name="articulo" class="table table-striped"><thead><tr><th>Id</th><th>Num.Sèrie</th><th>Descripció</th><th>Marca</th><th>Mòdel</th><th>Espai</th><th>Estat</th><th>Unitats</th>';
            var numeracio = false;
            if ($(this).parent().siblings().last().children().first().hasClass('inventari')){
                html += '<th>Numeració</th>';
                numeracio = true;
            }
            html += '<th>Operacions</th></tr></thead><tbody>';
            $(result.data).each(function (  i, item) {
                html += '<tr id="'+item.id+'"><td>'+item.id+'</td><td><span class="input" name="identificacion">'+item.identificacion+'</span></td><td><span class="input" name="descripcion">'+item.descripcion+
                    '</span></td><td><span class="input" name="marca">'+ item.marca+
                    '</span></td><td><span class="input" name="modelo">'+item.modelo+
                    '</span></td><td>'+item.espacio_id+'</td><td>'+
                    ESTADOS[item.estado]+'</td><td class="unidades">'+item.unidades+'</td>';
                if (numeracio){
                    html += '<td><span class="input" name="numeracionInventario">'+item.numeracionInventario+'</span></td>';
                }
                html += '<td><span class="botones">'+contenido1+'</span></td></tr>';
            });
            html += '</tbody></table>';
            $(".modal-title").text("Articles del Lot "+result.lote.descripcion);
            $(".modal-body").html(html);
            $(".modal-footer").find("button[type=submit]").hide();
            $(".modal-footer").find("button[type=button]").text("Cerrar");
        });

    })  

    if (autorizado) {
        // Botón DELETE
        $('#datalote').on('click', 'a.delete', function (event) {
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
                        $(this).parent().siblings('.unidades').text(0);
                        $('#1').children('.unidades').text(result.data.total);
                    }
                });
            } else {
                event.preventDefault();            
            }
        })  
        // Botón EDIT
        $('#datalote').on('click', 'a.edit', function (event) {
            event.preventDefault();
            $(this).attr("data-toggle","modal").attr("data-target", "#dialogo").attr("href","");
            var idLote = $(this).parent().siblings().first().text();
            var $descripcion = $(this).parent().siblings().eq(1);
            var $origen = $(this).parent().siblings().eq(2);
            var $proveedor = $(this).parent().siblings().eq(3);
            var $registre = $(this).parent().siblings().eq(4);


            let dlgControls=[
                {id: "Descripcion", type: "text", val: $descripcion.text()},
                {id: "Origen", type: "select"},
                {id: "Proveedor", type: "text", val: $proveedor.text()},
                {id: "Registre", type: "text", val: $registre.text()},
            ];
            $(".modal-title").text("Editar Lote");
            $(".modal-body").html(htmlDialog(dlgControls));
            $(".modal-footer").find("button[type=button]").text("Cancelar");
            $(".modal-footer").find("button[type=submit]").show().one("click", function() {
                $.ajax({
                    method: "PUT",
                    url: "/api/lote/"+idLote,
                    data: {
                        descripcion: $("#descripcion").val(),
                        procedencia: $("#origen").val(),
                        proveedor: $("#proveedor").val(),
                        registre: $("#registre").val(),
                        api_token: token,
                    },
                }).then(function (res) {
                    $descripcion.text($("#descripcion").val());
                    $origen.text(PROCEDENCIAS[$("#origen").val()]);
                    $proveedor.text($("#proveedor").val());
                    $registre.text($("#registre").val());
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
        // Botón COPY
        $('#datalote').on('click', 'a.copy', function (event) {
            if (selecionado === null){
                selecionado = $(this).parent().siblings().first().text();
                $(this).parent().parent().addClass('success');
            } else {
                if (selecionado === $(this).parent().siblings().first().text()){
                    selecionado = null;
                    $(this).parent().parent().removeClass('success');
                }
                else {
                    $.ajax({
                        context: this,
                        method: "PUT",
                        url: "/api/lote/" + $(this).parent().siblings().first().text()+'/articulos',
                        data: { api_token: token,
                                lote: selecionado},
                        dataType: "json",
                    }).then(function (result) {
                        if (result.success == true ){
                            $(this).parent().parent().addClass('danger');
                            $(this).parent().siblings('.unidades').text(0);
                            $('#'+selecionado).children('.unidades').text(result.data.total);
                        }
                    });
                }
            }
        })

        $('#datalote').on('click', 'a.inventari', function (event) {
            event.preventDefault();
            $.ajax({
                context: this,
                method: "PUT",
                url: "/api/lote/" + $(this).parent().siblings().first().text(),
                data: { api_token: token,
                    inventariable: 0},
                dataType: "json",
            }).then(function (result) {
                if (result.success == true ){
                    $(this).children().first().removeClass('fa-check-square-o');
                    $(this).children().first().removeClass('inventari');
                    $(this).children().first().addClass('noinventari');
                    $(this).children().first().addClass('fa-square-o');
                }
            });
        });
        $('#datalote').on('click', 'a.noinventari', function (event) {
            event.preventDefault();
            $.ajax({
                context: this,
                method: "PUT",
                url: "/api/lote/" + $(this).parent().siblings().first().text(),
                data: { api_token: token,
                    inventariable: 1},
                dataType: "json",
            }).then(function (result) {
                if (result.success == true ){
                    $(this).children().first().removeClass('fa-square-o');
                    $(this).children().first().removeClass('noinventari');
                    $(this).children().first().addClass('inventari');
                    $(this).children().first().addClass('fa-check-square-o');
                }
            });
        });
        // Botón DELETE article
        $('#dialogo').on('click', 'a.delete', function (event) {
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
                    url: "/api/articulo/" + $(this).parent().siblings().first().text()+'/lote',
                    data: { api_token: token},
                    dataType: "json",
                }).then(function (result) {
                    if (result.success == true ) {
                        if (result.data.total == 0){
                            $('#'+result.data.lote).addClass('danger');
                        }
                        else {
                            $('#'+result.data.lote).addClass('warning');
                        }
                        $('#'+result.data.lote).children('.unidades').text(result.data.total);
                        $(this).parent().siblings('.unidades').text(0);
                        $(this).parent().parent().addClass('danger');
                        $('#'+selecionado).children('.unidades').text(result.data.totalSeleccionado);
                    }
                });
            } else {
                event.preventDefault();
            }
        })
        // Botón COPY ARTICULOS
        $('#dialogo').on('click', 'a.copy', function (event) {
            if (selecionado !== null){
                $.ajax({
                    context: this,
                    method: "GET",
                    url: "/api/articulo/" + $(this).parent().siblings().first().text()+"/lote/"+selecionado,
                    data: { api_token: token},
                    dataType: "json",
                }).then(function (result) {
                    if (result.success == true ){
                        if (result.data.total_antiguo == 0){
                            $('#'+result.data.lote_antiguo).addClass('danger');
                        }
                        else {
                            $('#'+result.data.lote_antiguo).addClass('warning');
                        }
                        $('#'+result.data.lote_antiguo).children('.unidades').text(result.data.total_antiguo);
                        $('#'+result.data.lote_nuevo).children('.unidades').text(result.data.total_nuevo);
                        $(this).parent().siblings('.unidades').text(0);
                        $(this).parent().parent().addClass('danger');
                    }
                });
            }
        });

        $('#dialogo').on('click', 'a.edit', editRow);
        /** Botón EDIT
        $('#dialogo').on('click', 'a.edit', function (event) {
            event.preventDefault();
            $(this).attr("data-toggle","modal").attr("data-target", "#segundo").attr("href","");
            var idArticulo = $(this).parent().siblings().first().text()
            var $serie = $(this).parent().siblings().eq(1);
            var $descripcion = $(this).parent().siblings().eq(2);
            var $marca = $(this).parent().siblings().eq(3);
            var $modelo = $(this).parent().siblings().eq(4);
            var dlgControls=[
                {id: "Serie", type: "text", val: $descripcion.text()},
                {id: "Descripcion", type: "text", val: $serie.text()},
                {id: "Marca", type: "text", val: $marca.text()},
                {id: "Modelo", type: "text", val: $modelo.text()},
            ];
            if ($(this).parent().siblings().length == 10){
                var $numeracion = $(this).parent().siblings().eq(9);
                dlgControls.push({id: "Numeracion", type: "text", val: $numeracion.text()});
            }



            $(".modal-title").text("Editar Articulo");
            $(".modal-body").html(htmlDialog(dlgControls));
            $(".modal-footer").find("button[type=button]").text("Cancelar");
            $(".modal-footer").find("button[type=submit]").show().one("click", function() {
                $.ajax({
                    method: "PUT",
                    url: "/api/articulo/"+idArticulo,
                    data: {
                        descripcion: $("#descripcion").val(),
                        identificacion: $("#serie").val(),
                        marca: $("#marca").val(),
                        modelo: $("#modelo").val(),
                        numeracionInventario: $("#numeracion").val(),
                        api_token: token,
                    },
                }).then(function (res) {
                    $descripcion.text($("#descripcion").val());
                    $serie.text($("#serie").val());
                    $marca.text($("#marca").val());
                    $modelo.text($("#modelo").val());
                    $numeracion.text($("#numeracion").val());
                    console.log(res)
                }, function (res) {
                    console.log(res)
                });
                $("#segundo").modal("hide");
            });
        });
        */
        // Botón UNIDADES
        $('#datamaterial').on('click', 'a.unidades', function (event) {
            event.preventDefault();
            $(this).attr("data-toggle","modal").attr("data-target", "#dialogo").attr("href","");
            var idMaterial = $(this).parent().siblings().first().text();
            var $inputUnidades = $(this).parent().siblings().eq(4);
            var unidades = parseInt($inputUnidades.text());
            let dlgControls=[
                {id: "Unidades", type: "input", val: unidades},
                {id: "Explicacion", type: "textarea"},
            ];
            $(".modal-title").text("Cambiar unidades");
            $(".modal-body").html(htmlDialog(dlgControls));
            $(".modal-footer").find("button[type=button]").text("Cancelar");
            $(".modal-footer").find("button[type=submit]").show().one("click", function() {
                $.ajax({
                    method: "PUT",
                    url: "/api/material/cambiarUnidad",
                    data: {
                        id: idMaterial, 
                        unidades: $("#unidades").val(), 
                        unidades_antes: unidades, 
                        explicacion: $("#explicacion").val(),
                        api_token: token,
                    },
                }).then(function (res) {
                    $inputUnidades.text($("#unidades").val());
                    console.log(res)
                }, function (res) {
                    console.log(res)
                });
                $("#dialogo").modal("hide");

            });
        })  

        // Botón UBICACION
        $('#datamaterial').on('click', 'a.ubicacion', function (event) {
            event.preventDefault();
            $(this).attr("data-toggle","modal").attr("data-target", "#dialogo").attr("href","");
            var idMaterial = $(this).parent().siblings().first().text();
            var $inputEspacioTabla = $(this).parent().siblings().eq(3);
            var espacioTabla = $inputEspacioTabla.text();
            let dlgControls=[
                {id: "Espacios", type: "select"},
                {id: "Explicacion", type: "textarea"},
            ];
            $(".modal-title").text("Cambiar unidades");
            $(".modal-body").html(htmlDialog(dlgControls));
            $(".modal-footer").find("button[type=button]").text("Cancelar");
            $(".modal-footer").find("button[type=submit]").show().one("click", function() {
                $.ajax({
                    method: "PUT",
                    url: "/api/material/cambiarUbicacion",
                    data: {
                        id: idMaterial, 
                        ubicacion: $("#espacios").val(), 
                        ubicacion_antes: espacioTabla, 
                        explicacion: $("#explicacion").val(),
                        api_token: token,
                    },
                }).then(function (res) {
                    $inputEspacioTabla.text($("#espacios").val());
                    console.log(res)
                }, function (res) {
                    console.log(res)
                });
                $("#dialogo").modal("hide");
            });
            $.ajax({
                method: "GET",
                url: "/api/espacio",
                data: {api_token: token},
                dataType: "json",
            }).then(function (result) {
                $(result.data).each(function (i, item) {
                    $("#espacios").append('<option value="' + item.aula + (item.aula == espacioTabla?'" selected>':'">') + item.descripcion + '</option>');
                });
            });
                
        })  

        // Botón ESTADO
        $('#datamaterial').on('click', 'a.estado', function (event) {
            event.preventDefault();
            $(this).attr("data-toggle","modal").attr("data-target", "#dialogo").attr("href","");
            var idMaterial = $(this).parent().siblings().first().text();
            var $inputEstadoMaterial = $(this).parent().siblings().eq(2);
            var estadoMaterial = $inputEstadoMaterial.text();

            let dlgControls=[
                {id: "Estado", type: "select"},
                {id: "Explicacion", type: "textarea"},
            ];
            $(".modal-title").text("Cambiar unidades");
            $(".modal-body").html(htmlDialog(dlgControls));
            $(".modal-footer").find("button[type=button]").text("Cancelar");
            $(".modal-footer").find("button[type=submit]").show().one("click", function() {
                $.ajax({
                    method: "PUT",
                    url: "/api/material/cambiarEstado",
                    data: {
                        id: idMaterial, 
                        estado: $("#estado").val(), 
                        estado_antes: estadoMaterial, 
                        explicacion: $("#explicacion").val(),
                        api_token: token,
                    },
                }).then(function (res) {
                    $inputEstadoMaterial.text($("#estado").val());
                    console.log(res)
                }, function (res) {
                    console.log(res)
                });
                $("#dialogo").modal("hide");
            });
            $("#estado").append("<option value='1'"+ (estadoMaterial == 1?'" selected>':'">') + "OK </option>");
            $("#estado").append("<option value='2'"+ (estadoMaterial == 2?'" selected>':'">') + "Reparandose </option>");
            $("#estado").append("<option value='3'"+ (estadoMaterial == 3?'" selected>':'">') + "Baja </option>")
                
        })  
        // checkBox inventario
        $('#datamaterial').on('change', 'input.editor-active', function (event) {
            var idMaterial = $(this).parent().siblings().first().text();
            var inventariado = $(this).prop("checked");
                $.ajax({
                    method: "PUT",
                    url: "/api/material/cambiarInventario",
                    data: {
                        id: idMaterial, 
                        inventario:  inventariado, 
                        api_token: token,
                    },
            }).then(function (res) {
                    console.log(res)
                }, function (res) {
                    if (inventariado) {$(this).removeProp("checked");}
                    else {$(this).prop("checked");}
                    console.log(res)
            });
        })  
    }
})

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
