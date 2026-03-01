'use strict'

var dias=['L','M','X','J','V'];
var fecha=new Date('2017-10-17');
cambiaFecha(1-fecha.getDay());
loadFichas();

$(function() {
	// Pedimos las guardias
	$('.next-week').on('click', function(e) {
		e.preventDefault();
		cambiaFecha(+7, true);
	})
	$('.prev-week').on('click', function(e) {
		e.preventDefault();
		cambiaFecha(-7, true);
	})
})

function cambiaFecha(dias, borrar) {
	fecha.setDate(fecha.getDate()+dias);
	let fechaNextWeek=new Date(fecha);
	fechaNextWeek.setDate(fecha.getDate()+7);
	if (fechaNextWeek>new Date())
		$('.next-week').hide();
	else
		$('.next-week').show();
	$('#profe-title').children().next().nextAll().each(function(i) {
		if (i<5) {		// la de totales no la cambiamos
			var queFecha=new Date(fecha);
			queFecha.setDate(queFecha.getDate()+i);
			this.innerHTML=getFecha(queFecha);			
		}
	});
	if (borrar) {
		borraTabla();
		loadFichas();
	}
}

function borraTabla() {
	$('#tabla-datos').find('tbody').find('td').empty();
}

function apiAuthOptions() {
	var legacyToken = $.trim($("#_token").text());
	var bearerToken = $.trim($('meta[name="user-bearer-token"]').attr('content') || "");
	var options = {
		headers: {},
		data: {}
	};

	if (bearerToken) {
		options.headers.Authorization = "Bearer " + bearerToken;
	}
    if (legacyToken) {
		options.data.api_token = legacyToken;
	}

	return options;
}

function loadFichas() {
	let fechaFin=new Date(fecha);
	fechaFin.setDate(fecha.getDate()+4);
	let url="/api/faltaProfesor/horas/dia]"+getFecha(fecha)+"&dia["+getFecha(fechaFin);
	var auth = apiAuthOptions();
	$.ajax({
		url: url,
    	type: "GET",
    	dataType: "json",
		headers: auth.headers,
		data: auth.data
	}).then(function(res){
	    // Pintamos los datos
	    for (var profe in res.message) {
	    	let fila=$('#'+profe);
	    	let horasTot=0;
			$('#'+profe).children().next().nextAll().each(function(i) {
				if (i<5) {		// es celda de 1 día
					let queFecha=new Date(fecha);
					queFecha.setDate(queFecha.getDate()+i);
					if (res.message[profe][getFecha(queFecha)]) {	// si ha fichado ese día
						let horas=res.message[profe][getFecha(queFecha)].horas;
						this.innerHTML=horas;
						horasTot+=timeToSecs(horas);											
					}
				} else {		// es celda de totales
					this.innerHTML='<b>'+secsToTime(horasTot)+'</b>';
				}
			});
		}
	}, function(error){
		console.error("Error "+error.status+": "+error.statusText, "error");
	});
}

function getFecha(fecha) {
	return fecha.toISOString().split('T')[0];
}

function secsToTime(secs) {
	let hours=parseInt(secs/(60*60));
	secs-=hours*60*60;
	let minutes=parseInt(secs/60);
	secs-=minutes*60;
	return fillZero(hours)+':'+fillZero(minutes)+':'+fillZero(secs);
}

function timeToSecs(time) {
	let separatedTime=time.split(':');
	return separatedTime[0]*60*60+separatedTime[1]*60+Number(separatedTime[2]);
}

function fillZero(value, digits=2) {
	let filled="0000000000"+value;
	return filled.substr(filled.length - digits);
}
