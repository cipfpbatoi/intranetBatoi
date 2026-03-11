/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

const maxDiasReserva=30;
const minDiasReserva=3;
const esDireccion=2;

function apiAuthOptions(extraData) {
	var bearerMeta = document.querySelector('meta[name="user-bearer-token"]');
	var bearerToken = ((bearerMeta ? bearerMeta.getAttribute('content') : '') || '').trim();
	var data = extraData || {};
	var headers = {};

	if (bearerToken) {
		headers.Authorization = "Bearer " + bearerToken;
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

function parseJsonSafe(response) {
	return response.text().then(function(text) {
		if (!text) {
			return {};
		}
		try {
			return JSON.parse(text);
		} catch (e) {
			return {};
		}
	});
}

function apiRequest(method, url, extraData) {
	var auth = apiAuthOptions(extraData);
	var options = {
		method: method,
		headers: Object.assign({}, auth.headers),
		credentials: 'same-origin'
	};

	if (method === 'GET') {
		url = withQueryParams(url, auth.data);
	} else {
		options.headers['Content-Type'] = 'application/x-www-form-urlencoded; charset=UTF-8';
		options.body = new URLSearchParams(auth.data).toString();
	}

	return fetch(url, options).then(function(response) {
		if (!response.ok) {
			var error = new Error('HTTP ' + response.status);
			error.status = response.status;
			error.statusText = response.statusText;
			throw error;
		}
		return parseJsonSafe(response);
	});
}

$(function() {
    var errores = [];

	$(".errores").empty();
	$("#recurso").focus();
	$("#gestion").hide();	// Ocultamos los inputs de añadir

    //datepickers ui
	var maxFecha=new Date();
	maxFecha.setDate(maxFecha.getDate()-minDiasReserva);
	$('#dia').attr('minDate',maxFecha.toISOString().substr(0,10));
	if ($('#rol').text()%esDireccion == 0) {

		$('#dia').removeAttr('max');
		$("#periodica").show();
	} else {
		var maxFecha=new Date();
		maxFecha.setDate(maxFecha.getDate()+maxDiasReserva);
		$('#dia').attr('max',maxFecha.toISOString().substr(0,10));
		$("#periodica").hide();
	}

	$("#recurso").on("change", function () {
		if ($("#recurso").val()!=0) {	// Hay un recurso seleccionado
			$("#gestion").show();	// Ocultamos los inputs de añadir
			if ($("#dia").val()) {		// Si hay fecha se están iendo las reservas de ese día y hay que cambiarlas
				$("#dia").trigger('change');	
			}
		} else {
			$("#gestion").hide();	// Ocultamos los inputs de añadir
		}
	});

	$("#next").on("click",function (ev) {
		ev.preventDefault();
		var fecha =$("#dia").val();
		if (fecha) {		// Si hay fecha se están iendo las reservas de ese día y hay que cambiarlas
			var queFecha=new Date(fecha);
			queFecha.setDate(queFecha.getDate()+1);
			fecha=getFechaInt(queFecha);
			$("#dia").val(fecha);
			$("#dia").trigger('change');
		}
	})

	$(".hora").on("click",function (ev) {
		ev.preventDefault();
		var hora = this.id.split('-')[1];
		var desde = parseInt($("#desde").val());
		$("#hasta").val(hora);
		if (desde == 0 || parseInt(hora) < desde) {
			$("#desde").val(hora);
		}
		marcar();
	})


	$("#back").on("click",function (ev) {
		ev.preventDefault();
		var fecha =$("#dia").val();
		if (fecha) {		// Si hay fecha se están iendo las reservas de ese día y hay que cambiarlas
			var queFecha=new Date(fecha);
			queFecha.setDate(queFecha.getDate()-1);
			fecha=getFechaInt(queFecha);
			$("#dia").val(fecha);
			$("#dia").trigger('change');
		}
	})

	$("#forward").on("click",function (ev) {
		ev.preventDefault();
		var fecha =$("#dia").val();
		if (fecha) {		// Si hay fecha se están iendo las reservas de ese día y hay que cambiarlas
			var queFecha=new Date(fecha);
			queFecha.setDate(queFecha.getDate()+7);
			fecha=getFechaInt(queFecha);
			$("#dia").val(fecha);
			$("#dia").trigger('change');
		}
	})

	$("#reward").on("click",function (ev) {
		ev.preventDefault();
		var fecha =$("#dia").val();
		if (fecha) {		// Si hay fecha se están iendo las reservas de ese día y hay que cambiarlas
			var queFecha=new Date(fecha);
			queFecha.setDate(queFecha.getDate()-7);
			fecha=getFechaInt(queFecha);
			$("#dia").val(fecha);
			$("#dia").trigger('change');
		}
	})

	$("#dia").on("change", function () {
		// borramos los datos actuales
		$("#horario").find("td").removeClass("warning").html("Lliure");

		var fecha =$("#dia").val();
		var queFecha=new Date(fecha);
		// Ponemos el día en la fecha de fin de reserva para dirección
		var nomDias=["Diumenge", "Dilluns", "Dimarts", "Dimecres", "Dijous", "Divendres", "Dissabte"];

		$("#nom_dia_fin").html(nomDias[queFecha.getDay()]);
		$("#nom_dia_semana").html(nomDias[queFecha.getDay()]);

		// pedimos las reservas del recurso para el día seleccionado
		apiRequest('GET', 'api/reserva', {
			idEspacio: $("#recurso").val(),
			dia: fecha
		}).then(function(res){
			(res.data || []).forEach(function(item) {
				var observaciones = item.observaciones ? '(' + item.observaciones + ')' : ' ';
				$("#hora-"+item.hora).addClass("warning").html(item.nomProfe + observaciones + '<span class="hidden idProfe">' + item.idProfesor + '</span>' + '<span class="hidden idReserva">' + item.id + '</span>');
			});
		}, function(error){
			showMessage(["Error "+(error.status || '')+": "+(error.statusText || 'Error')], 'error');
		});
	});

	$("#desde").on("change", function () {
		var indice = $("#desde option:selected").index()
		$("#hasta").val( $("#desde").val() );
		$("#hasta").children().slice(0, indice).attr("disabled","disabled");
		$("#hasta").children().slice(indice, $("#desde option").length).removeAttr("disabled");
		if ($("#desde").val()>0) {	// Hay un recurso seleccionado
			$("#hasta").val( $("#desde").val() );
		} else {
			$("#desde").val(0);
		}
		marcar();
		click = 1;
	});
	$("#hasta").on("change", function () {
		marcar();
		click = 0;
	});

	$("#reservar").on("click", function (ev) {
	    errores.length=0;
		ev.preventDefault();
		if (checkData()) {
			// Antes de reservar comprobamos que todas las horas estén libres
			$("#horario td").slice($("#desde").val()-1, $("#hasta").val()).each(function() {
					if ($(this).text()!="Lliure")
						errores.push("En l'interval indicat no està lliure l'hora "+$(this).prev().text());
				});
			if (errores.length==0) 
				modDatos("reserva");
			else
				showMessage(errores, 'error');
		}
	});

	$("#liberar").on("click", function (ev) {
	    errores.length=0;
		ev.preventDefault();
		if (checkData()) {
			// Antes de liberar comprobamos que todas las horas las haya reservado este profe o que sea de dirección
			if ($('#rol')%esDireccion == 0) {
				// Si es direccion se libera y punto
				modDatos("libera");
			} else {
				$("#horario td").slice($("#desde").val()-1, $("#hasta").val()).each(function() {
					if ($(this).text()!="Lliure" && $(this).find('span.idProfe').text()!=$('#dni').text())
						errores.push("En l'interval indicat l'hora "+$(this).prev().text()+" està reservada per "+$(this).text() );
					});
				if (errores.length==0) 
					modDatos("libera");
				else
					showMessage(errores, 'error');
			}
		}
	});

});

function checkData() {
    var errores = [];

    // recurso
    if ($("#recurso").val()=="0") {
        errores.push("el camp 'Recurs' ha d'estar seleccionat");
	}
    if ($("#observaciones").val().length>20){
        errores.push("el camp 'Observacions' té un màxim de 20 caracters");
	}

    // dia
    var fecha = $("#dia").val();
    if (fecha=="") {
        errores.push("el camp 'Dia' ha d'estar seleccionat");
	}

    // desde
	var desde = parseInt( $("#desde").val() );
    if (desde==0) {
        errores.push("el camp 'Des d'hora' ha d'estar seleccionat");
	}

    // hasta
	var hasta=parseInt($("#hasta").val());
    if (hasta==0) {
        errores.push("el camp 'fins hora' ha d'estar seleccionat");
	} else if (hasta < desde)
        errores.push("el camp 'fins hora' ha de ser major que 'des d'hora'");

    // dia_fin
    var fecha_fin = $("#dia_fin").val();
    if (fecha_fin!="")
//    {
//        errores.push("el campo 'Hasta Dia' debe tener formato dd/mm/aaaa");
//	} else 
		if (fecha_fin < fecha) {
        	errores.push("el camp 'fins dia' ha de ser major que 'dia'");
		}
	if (errores.length>0) {
		showMessage(errores,'error');
		return false;
	} else {
		return true;
	}
}

function modDatos(accion) {
	var fecha=$("#dia").val();
	var fechaDate=new Date(fecha);
    var peticiones=[];	// donde guardo las respuestas para saber si ya ha acabado el proceso

	if (accion=="reserva") {
		var msg="Se va a reservar el recurso entre las fechas indicadas. Si hubiera alguna hora ya reservada fallará la reserva. ¿Deseas continuar?";
	} else {
		var msg="Se van a liberar todas las reservas del recurso entre las fechas indicadas ¿Deseas continuar?";
	}
	if (!confirm(msg))
		return false;

	if (accion=="reserva") {
		var datosBase={
				idEspacio: $("#recurso").val(), 
				idProfesor: $('#idProfesor').val(),
				observaciones: $('#observaciones').val()
			};
		if ($("#dia_fin").val()) {
			var fechaFin=$("#dia_fin").val();
			var fechaFinDate=new Date(fechaFin);
		} else {
			var fechaFinDate=new Date(fecha);			
		}
		while (fechaDate<=fechaFinDate) {
		    for (var i=Number($("#desde").val()); i<=Number($("#hasta").val()); i++) {
		    	peticiones.push({fecha: fecha, hora: i});
		    }
			fechaDate.setDate(fechaDate.getDate()+7);
			fecha=getFechaInt(fechaDate);
		}
	} else {
		var datosBase={};
	    for (var i=Number($("#desde").val()); i<=Number($("#hasta").val()); i++) {
	    	if ($('#hora-'+i).find('span.idReserva').text()) {
	    		peticiones.push({fecha: fecha, hora: $('#hora-'+i).find('span.idReserva').text()});
	    	}
	    }
	}

	if (peticiones.length === 0) {
		showMessage(["No hi ha hores per a processar"], 'error');
		return false;
	}

	var promises = peticiones.map(function(peticion) {
		if (accion === 'reserva') {
			var payload = Object.assign({}, datosBase, {
				dia: peticion.fecha,
				hora: peticion.hora
			});
			return apiRequest('POST', 'api/reserva', payload);
		}

		return apiRequest('DELETE', 'api/reserva/' + peticion.hora, datosBase);
	});

	Promise.allSettled(promises).then(function(results) {
		var hasErrors = results.some(function(result) {
			if (result.status !== 'fulfilled') {
				return true;
			}
			if (result.value && Object.prototype.hasOwnProperty.call(result.value, 'success')) {
				return result.value.success !== true;
			}
			return false;
		});

		if (hasErrors) {
			showMessage(["Algunas horas no se han podido "+accion+"r"], 'error');
		} else {
			showMessage(["El recurso se ha "+accion+"do correctamente"], 'ok');
		}
		$("#dia").trigger('change');
	});

    marcar();
}

function showMessage(msgs, tipo) {
	var $msg=$(`
		<div id="msg" class="alert `+(tipo=='error'?`alert-danger `:`alert-success `)+`alert-block fade in">
			<button type="button" class="close" data-dismiss="alert">×</button></div>`).appendTo($('.x_content').last());
	msgs.forEach(function(msg) {
		$msg.append(`<p><strong>`+msg+`</strong></p>`);
	});
	$("#errores").html($msg);
}

function getFechaInt(fechaDate) {
	var mes='0'+(fechaDate.getMonth()+1);
	mes=mes.substr(mes.length-2,2);
	var dia='0'+fechaDate.getDate();
	dia=dia.substr(dia.length-2,2);
	return fechaDate.getFullYear()+'-'+mes+'-'+dia;
}

function marcar(){
	var desde = parseInt($("#desde").val());
	var hasta = parseInt($("#hasta").val())?parseInt($("#hasta").val()):desde;
	for (i=1;i<=20;i++) {
		$("#hora-"+i).removeClass("green").removeClass("red");
	}
	for (j=desde;j<=hasta;j++) {
		if ($("#hora-"+j).text() == "Lliure") {
			$("#hora-" + j).addClass("green");
		} else {
			$("#hora-" + j).addClass("red");
		}
	}
}
