'use strict'

var dias=['Lun','Mar','Mie','Jue','Vie'];
var fecha=new Date();
//cambiaFecha(1-fecha.getDay());
loadFichas();
loadHorario();

$(function() {
	// Pedimos las guardias
	$('.next-day').hide().on('click', function(e) {
		e.preventDefault();
		cambiaFecha(+1, true);
	})
	$('.prev-day').on('click', function(e) {
		e.preventDefault();
		cambiaFecha(-1, true);
	})
})

function cambiaFecha(dias, borrar) {
	fecha.setDate(fecha.getDate()+dias);
	if (fecha.getDay()==0)	// Es Domingo
		fecha.setDate(fecha.getDate()-2);
	if (fecha.getDay()==6)	// Es Sábado
		fecha.setDate(fecha.getDate()+2);
	if (getFecha(fecha) >= getFecha(new Date()))
		$('.next-day').hide();
	else
		$('.next-day').show();
	if (borrar) {
		borraTabla();
		loadFichas();
		loadHorario();
	}
}

function borraTabla() {
	$('#tabla-datos').find('tbody').find('td>span.fichaje').empty();
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
	} else if (legacyToken) {
		options.data.api_token = legacyToken;
	}

	return options;
}

function loadFichas() {
	let url="/api/faltaProfesor/dia="+getFecha(fecha);
	var auth = apiAuthOptions();
	$.ajax({
		url: url,
    	type: "GET",
    	dataType: "json",
		headers: auth.headers,
		data: auth.data
	}).then(function(res){
	    // Pintamos los datos
	    let fichadas=[];
	    for (var i in res.data) {
	    	let ficha=res.data[i];
	    	let fila=document.getElementById(ficha.idProfesor);
	    	let horasTot=0;
			fila.children[3].querySelector('.fichaje').innerHTML+=
				ficha.entrada+'->'+ficha.salida+'<br>';
		}
		// Ponemos la fecha
		$('#profe-title').children().last().text(dias[fecha.getDay()-1]+' '+getFecha(fecha));
	}, function(error){
		console.error("Error "+error.status+": "+error.statusText, "error");
	});
}

function loadHorario() {
	let url="/api/horariosDia/"+getFechaEsp(fecha);
	var auth = apiAuthOptions();
	$.ajax({
		url: url,
    	type: "GET",
    	dataType: "json",
		headers: auth.headers,
		data: auth.data
	}).then(function(res){
	    // Pintamos los datos
	    let horarios=[];
	    for (var i in res.data) {
	    	let horario=res.data[i];
	    	let fila=document.getElementById(i);
	    	let horasTot=0;
			fila.children[2].querySelector('.fichaje').innerHTML+=
				horario;
		}
		// Ponemos la fecha
		$('#profe-title').children().last().text(dias[fecha.getDay()-1]+' '+getFecha(fecha));
	}, function(error){
		console.error("Error "+error.status+": "+error.statusText, "error");
	});
}

function getFecha(fecha) {
	return fecha.toISOString().split('T')[0];
}

function getFechaEsp(fecha) {
	var fechaInt=getFecha(fecha).split('-');
	return fechaInt[2]+'-'+fechaInt[1]+'-'+fechaInt[0];
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
