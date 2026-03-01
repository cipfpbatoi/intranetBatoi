'use strict';

const PROCEDENCIAS=['Desconocida', 'Dotación', 'Compra', 'Donación'];
const ADMINISTRADOR=11;
const MANTENIMIENTO=7;
const mesesCaduca=6;

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


    // FUncionalidades de los botones
    var admin=(!($('#rol').text()%ADMINISTRADOR));
    var autorizado=(!($('#rol').text()%MANTENIMIENTO));

     // Código para pintar los botones de la datatable
    var contenido="";
    if (admin) {
        contenido+=            `
                <a href="#" class="edit">
                    <i class="fa fa-pencil" title="Editar"></i>
                </a> 
                <a href="#" class="delete">
                    <i class="fa fa-trash" title="Borrar"></i>
                </a>
                `;
    }
    contenido+=`
                <a href="#" class="ver">
                    <i class="fa fa-eye" title="Ver"></i>                
                </a>
    `;
    if (autorizado) {
        contenido+=` 
                     
                <a href="#" class="ubicacion">
                    <i class="fa fa-map-marker" title="Cambiar ubicación"></i>                
                </a>
                <a href="#" class="estado">
                    <i class="fa fa-toggle-on" title="Cambiar estado"></i>                
                </a>`
    }
    var checkbox = "<div class='icheckbox_flat-green'><input type='checkbox' class='flat'></div>";
    var authDatatable = apiAuthOptions();
    var espai = $("#search").text();
    var article = $("#article").text();
    $("#datamaterial").DataTable( {
        "columnDefs":[
            {
                "targets" : [1],
                "visible" : false,
            }
        ],
        "searchCols": [
            null,
            { "search": article},
            null,
            null,
            null,
        ],
        ajax : {
            method: "GET",
            url: '/api/inventario/'+espai,
            headers: authDatatable.headers,
            data: authDatatable.data,
        },
        deferRender : true,
        dataSrc : 'data',
        dom: 'Bfrtip',
        buttons: [
            'print'
        ],
        columns: [
            { data:'id'},
            { data:'articulo'},
            { data:'descripcion'},
            { data: 'estado'},
            { data:'espacio'},
            { data: null, defaultContent: contenido},
            { data: null ,
              render: function (data, type, row ) {
                    var fechaUltimo=new Date(data.fechaultimoinventario);
                    var fechaCaduca=new Date();
                    fechaCaduca.setMonth(fechaCaduca.getMonth() - mesesCaduca);
                    if ( type === 'display' ) {
                        if (fechaUltimo > fechaCaduca)
                            return '<input type="checkbox" checked class="editor-active">';
                        else 
                            return '<input type="checkbox" class="editor-active">';
                    }
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

    $('#datamaterial tbody').on( 'click', 'tr td:first-child', function () {
        $(this).parent().toggleClass('selected');
    } );

    $('#printCodeBar').on( 'click', function (event) {
        event.preventDefault();
        var list = $('#datamaterial').DataTable().rows('.selected');
        var ids = list.data().map((row) => row.id );
        if (ids.count()){
            $('#idList').val(ids.toArray());
            $('#printCodeBars').submit();
        } else {
            alert('Has de seleccionar primer les etiquetes polsant sobre la seua clau');
        }
    });



    // Botón VER
    $('#datamaterial').on('click', 'a.ver', function (event) {
        event.preventDefault();
        $(this).attr("data-toggle","modal").attr("data-target", "#dialogo").attr("href","");
        var idMaterial = $(this).parent().siblings().first().text();
        $.ajax({
            method: "GET",
            url: "/api/material/" + idMaterial,
            headers: apiAuthOptions().headers,
            data: apiAuthOptions().data,
            dataType: "json",
        }).then(function (result) {
            let dlgControls=[
                {id: "NumSerie", type: "span", val: result.data.nserieprov},
                {id: "Descripcion", type: "span", val: result.data.descripcion},
                {id: "Marca", type: "span", val: result.data.marca},
                {id: "Modelo", type: "span", val: result.data.modelo},
                {id: "Procedencia", type: "span", val: PROCEDENCIAS[result.data.procedencia]},
                {id: "Estado", type: "span", val: result.data.estado},
                {id: "Espacio", type: "span", val: result.data.espacio},
                {id: "Unidades", type: "span", val: result.data.unidades},
                {id: "Isbn", type: "span", val: result.data.ISBN},
                {id: "FechaUltimoInventario", type: "span", val: result.data.fechaultimoinventario},
                {id: "Tipo", type: "span", val: result.data.tipo},
                {id: "Proveedor", type: "span", val: result.data.proveedor},
            ];
            $(".modal-title").text("Ver Material");
            $(".modal-body").html(htmlDialog(dlgControls));
            $(".modal-footer").find("button[type=submit]").hide();
            $(".modal-footer").find("button[type=button]").text("Cerrar");
        });

    });

    // Botón INCIDENCIA
    $('#datamaterial').on('click', 'a.incidencia', function (event) {
        $(this).attr("href","/material/"+$(this).parent().siblings().first().text()+"/incidencia");
    });

    if (autorizado) {
        // Botón DELETE
        $('#datamaterial').on('click', 'a.delete', function (event) {
            let info="\n";
            let titles=$(this).parents('table').find('thead').find('th');
            $(this).parent().siblings().each(function(i, item) {
                if (item.innerHTML.trim().length>0) {
                    info+=` - ${titles.eq(i).text().trim()}: ${item.innerHTML}\n`;                  
                }
            })
            if (confirm('Vas a donar de baixa el material:'+info)) {
                $(this).attr("href","/material/"+$(this).parent().siblings().first().text()+"/delete");
            } else {
                event.preventDefault();            
            }
        })  
        // Botón EDIT
        $('#datamaterial').on('click', 'a.edit', function (event) {
            $(this).attr("href","/inventario/"+$(this).parent().siblings().first().text()+"/edit");
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
            $(".modal-title").text("Canviar Ubicació");
            $(".modal-body").html(htmlDialog(dlgControls));
            $(".modal-footer").find("button[type=button]").text("Cancelar");
            $(".modal-footer").find("button[type=submit]").off("click").show().one("click", function() {
                var authUbicacion = apiAuthOptions({
                    id: idMaterial,
                    ubicacion: $("#espacios").val(),
                    ubicacion_antes: espacioTabla,
                    explicacion: $("#explicacion").val(),
                });
                $.ajax({
                    method: "PUT",
                    url: "/api/material/cambiarUbicacion",
                    headers: authUbicacion.headers,
                    data: authUbicacion.data,
                }).then(function (res) {
                    $inputEspacioTabla.text(res.data.updated);
                    console.log(res)
                }, function (res) {
                    console.log(res)
                });
                $("#dialogo").modal("hide");
            });
            $.ajax({
                method: "GET",
                url: "/api/espacio",
                headers: apiAuthOptions().headers,
                data: apiAuthOptions().data,
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
                {id: "Explicacion", type: "textarea"},
            ];
            $(".modal-title").text("Donar de Baixa");
            $(".modal-body").html(htmlDialog(dlgControls));
            $(".modal-footer").find("button[type=button]").text("Cancelar");
            $(".modal-footer").find("button[type=submit]").show().one("click", function() {
                var authEstado = apiAuthOptions({
                    id: idMaterial,
                    estado: 3,
                    estado_antes: estadoMaterial,
                    explicacion: $("#explicacion").val(),
                });
                $.ajax({
                    method: "PUT",
                    url: "/api/material/cambiarEstado",
                    headers: authEstado.headers,
                    data: authEstado.data,
                }).then(function (res) {
                    $inputEstadoMaterial.text(res.data.updated);
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
                var authInventario = apiAuthOptions({
                    id: idMaterial,
                    inventario: inventariado,
                });
                $.ajax({
                    method: "PUT",
                    url: "/api/material/cambiarInventario",
                    headers: authInventario.headers,
                    data: authInventario.data,
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
