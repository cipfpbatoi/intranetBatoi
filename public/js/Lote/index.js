'use strict';

const PROCEDENCIAS=['Desconocido', 'Dotación', 'Compra', 'Donación'];
const ESTADOS=[ 'BUIDA','ALTA', 'INVENTARIANT','FINALITZADA'];
const MANTENIMIENTO=7;
var selecionado = null;
var options = {};

function apiAuthOptions(extraData) {
    var legacyToken = $.trim($("#_token").text());
    var bearerToken = $.trim($('meta[name="user-bearer-token"]').attr('content') || "");
    var data = extraData || {};
    var headers = {};

    if (bearerToken) {
        headers.Authorization = "Bearer " + bearerToken;
    }
    if (legacyToken) {
        data.api_token = legacyToken;
    }

    return { headers: headers, data: data };
}

function withQueryParams(url, params) {
    var query = new URLSearchParams(params || {}).toString();
    if (!query) {
        return url;
    }
    return url + (url.indexOf('?') === -1 ? '?' : '&') + query;
}

function toFormBody(data) {
    var params = new URLSearchParams();
    Object.keys(data || {}).forEach(function (key) {
        if (data[key] !== undefined && data[key] !== null) {
            params.append(key, String(data[key]));
        }
    });
    return params.toString();
}

function requestJson(method, url, payload) {
    var auth = apiAuthOptions(payload || {});
    var options = {
        method: method,
        headers: Object.assign({}, auth.headers),
        credentials: 'same-origin'
    };
    var finalUrl = url;

    if (method === 'GET' || method === 'DELETE') {
        finalUrl = withQueryParams(url, auth.data);
    } else {
        options.headers['Content-Type'] = 'application/x-www-form-urlencoded; charset=UTF-8';
        options.body = toFormBody(auth.data);
    }

    return fetch(finalUrl, options).then(function (response) {
        if (!response.ok) {
            return response.text().then(function (text) {
                var error = new Error('HTTP ' + response.status);
                error.responseText = text;
                throw error;
            });
        }
        return response.json();
    });
}


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
    var auth = apiAuthOptions();
    var articulos = cargaArticulos();
    $("#datatable").DataTable( {
        ajax : {
            method: "GET",
            url: '/api/lote',
            headers: auth.headers,
            data: auth.data,
        },
        deferRender : true,
        dataSrc : 'data',
        rowId : 'registre',
        order: [[ 5, "desc" ]],
        columnDefs: [
            {className: "estado", "targets": [ 4 ]} ,
            {className: "operaciones", "targets": [ 7 ]} ,
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
            { data:'departamento'},
            { data: null, render: function (data){
                return  (data.estado == 1) ? contenido+operaciones+inventariable:
                        (data.estado == 2) ? contenido+editar:
                        (data.estado == 0) ? contenido+operaciones+capturar:contenido+editar+`<a href="/direccion/lote/`+data.registre+`/print" class="QR">
                                <i class="fa fa-barcode" title="Codi QR"></i>                
                            </a>`;
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
                var current = this;
                requestJson("DELETE", "/api/lote/" + $(this).parent().siblings().first().text(), {}).then(function (result) {
                    if (result.success == true ) {
                        $(current).parent().parent().addClass('danger');
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
                requestJson("PUT", "/api/lote/"+$registre.text(), {
                    registre: $("#registre").val(),
                    factura: $("#factura").val(),
                    proveedor: $("#proveedor").val(),
                    procedencia: $("#origen").val(),
                    fechaAlta: $("#fechaalta").val(),
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
            var texto = 'Vas a inventariar el lot amb registre '+idLote+', amb els següents articles i avisar al cap de departament.\n';
            var current = this;
            requestJson("GET", "/api/lote/" + idLote+"/articulos", {}).then(function (result) {
                $(result.data).each(function (  i, item) {
                    texto += item.unidades + ' de '+ item.descripcion+'\n';
                });
                if (confirm(texto)) {
                    requestJson("PUT", "/api/lote/" + idLote +'/articulos', {inventariar: true}).then(function (result) {
                        if (result.success == true ){
                            $(current).parent().parent().addClass('danger');
                            $(current).parent().siblings('.estado').text("INVENTARIANT");
                            $(current).parent().html(contenido);
                        }
                    }).catch(function (error) {
                        console.log(error);
                    });
                }
            });
        });

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
            var current = this;
            requestJson("GET", "/api/espacio", {}).then(function (result) {
                $(result.data).each(function (i, item) {
                    options[item.aula] = item.descripcion ;
                });
                cargaMateriales(current,idArticulo);
            });
        })
        $('#dialogo').on('click', 'a.new', function (event,idLote) {
            event.preventDefault();
            var current = this;
            requestJson("POST", "/api/articuloLote", {
                lote_id: $("#idLote").text(),
                articulo_id: $("#articulo_id").val(),
                descripcion: $("#descripcion").val(),
                marca: $("#marca").val(),
                modelo: $("#modelo").val(),
                unidades: $("#unidades").val(),
            }).then(function (result) {
                if (result.success == true ) {
                    cargaArticulosLote(current,$("#idLote").text(),true)
                }
            }).catch(function (error) {
                console.log(error);
            });
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
                var current = this;
                requestJson("DELETE", "/api/articuloLote/" + $(this).parent().parent().siblings().first().text(), {}).then(function (result) {
                    if (result.success == true ) {
                        cargaArticulosLote(current,$("#idLote").text(),true)
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
    requestJson("GET", "/api/lote/" + idLote+"/articulos", {}).then(function (result) {
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
    }).catch(function (error) {
        console.log(error);
    });
}

function cargaArticulos() {
    requestJson("GET", "/api/articulo", {}).then(function (result) {
        articulos = '<select id="articulo_id" name="articulo_id" ><option value="new">Article Nou</option>';
        $(result.data).each(function (i, item) {
            if (i == 0) {
                articulos += "<option value='" + item.id + "' selected>" + item.descripcion + "</option>";
            } else {
                articulos += "<option value='" + item.id + "'>" + item.descripcion + "</option>";
            }
        });
    }).catch(function (error) {
        console.log(error);
    });
}

function cargaMateriales(entorno,idArticulo){
    $(entorno).attr("data-toggle","modal").attr("data-target", "#materiales").attr("href","");
    requestJson("GET", "/api/articuloLote/" + idArticulo +"/materiales", {}).then(function (result) {
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
    }).catch(function (error) {
        console.log(error);
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
