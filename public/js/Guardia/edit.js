'use strict'

const MaxDiasAtras=7;	// nº de días que puede cambiar atrás
const ocupacionGuardia=[
	{
		cod: 3249454,
		descrip: 'Sala de Profes'
	},{
		cod: 149034734,
		descrip: 'Biblioteca'
	}
];

var miIP='';		// la IP de donde estamos
var sesion=0;		// value del tramo horario en que estamos
var guardiasSemana=[];	// datos de las guardias del profesor
var idGuardia=0;	// indica si ya se ha registrado esta guardia
var ipGuardia = [];
var biblio=false;	// indica si está en el sitio: en la biblio si es guardia de biblio
					// o en la Sala de profes si es guardia normal
var codLugar=0;		// indica el código de ocupación del sitio donde estamos:
					// 3249454->Sala profes, XXXX->Biblio, 0->resto
var diaHoy="";
var diaSelec="";
var horaActual="";
var dias_semana=["D", "L", "M", "X", "J", "V", "S"];

$(function() {
	$.ajax({
    	url: "/api/miIp",
    	type: "GET",
    	dataType: "json",
        data: {
    		api_token: $("#_token").text()
    	}
        })
		.then(
			(response) => miIP = response.data)
    $.ajax ({
    	url: "/api/ipGuardia",
    	type: "GET",
    	dataType: "json",
        data: {
    		api_token: $("#_token").text()
    	}
        }).then(function(res){
			ipGuardia = res.data;
			if (miIP && !codLugar)	// cuando se recibió la IP no estaba esto
				setPlace();
        });    
        
	//Test: Print the IP addresses into the console
//	getUserIP(function(ip){
//		showMessage("IP "+ip, "error");
//		miIP = ip;
		if (ipGuardia.length)	// Ya hemos recibido las IP donde hacer guardias
			setPlace();
//    biblio=true;
//    cambiaHora();
    $('#hora option').attr('disabled', 'disabled');
	$('#hora').prepend('<option value="0">-- Seleciona --</option>');
	$('#hora').val(0);				
	// Miramos la fecha y hora actuales y configuramos el datepicker
	var ahora=new Date();
	diaHoy=ahora.toISOString().split('T')[0];
	$('#dia').attr('maxDate', diaHoy);
	$("#dia").val(ahora.getDate()+"-"+(ahora.getMonth()+1)+"-"+ahora.getFullYear());
	diaSelec=diaHoy;
//	var hora=ahora.toLocaleTimeString().substr(0,5);
	horaActual=ahora.toTimeString().substr(0,5);
	setSesion();
//hora="17:00";
	// Pediremos el horario del profesor para ver las guardias y los tramos horarios
	// Cuando los tengamos habilitamos esas horas en el select de tramos horarios
	$.ajax ({
    	url: "/api/horario/"+$('#dni').text()+"/guardia",
//url: "api/horario/idProfesor=021666373M&ocupacion=3249454&dia_semana="+dias_semana[ahora.getDay()],
    	type: "GET",
    	dataType: "json",
        data: {api_token: $("#_token").text()},
	}).then(function(res){
	    // las guardias llegan como objetos no como array
//	    guardiasSemana=res.data;
		for (var i in res.data) {
			guardiasSemana.push(res.data[i]);
		}
	    // Habilitamos los tramos en que el profesor tiene guardia
	    habilitaHoras(ahora);
	}, function(error){
		showMessage("Error "+error.status+": "+error.statusText, "error");
	});

    // Activamos los eventos
	$("#dia").on("dp.change", function() {
		if (dateEspToISO( $('#dia').val() )!==diaSelec) {
			// Si no ha cambiado el día no hacemos nada
			diaSelec=dateEspToISO( $('#dia').val());
			var fechaSel=new Date( diaSelec );
		    // Habilitamos los tramos en que el profesor tiene guardia ese día
			habilitaHoras(fechaSel);
			// Activamos o desactivamos el botón de Guardar
			if (fechaSel > ahora) {
				$('form').find('#submit').attr('disabled','disabled');
			} else {
				fechaSel.setDate(fechaSel.getDate()+MaxDiasAtras);
				if (fechaSel<ahora)
					$('form').find('#submit').attr('disabled','disabled');
				else
					$('form').find('#submit').removeAttr('disabled');
			}
		}
	});
	$("#hora").on("change", cambiaHora);
	$("#submit").on("click", modDatos);    
})

function habilitaHoras(fecha) {
    var horas=guardiasSemana.filter(guardia=>guardia.dia_semana==dias_semana[fecha.getDay()] );
	var guardias_hoy=horas.length;
    for (var i in horas) {
		$('#hora option[value='+horas[i].sesion_orden+']').removeAttr('disabled');
    }
    if (!guardias_hoy) {
    	// Acabamos antes de poner la funcionalidad de la página
		showMessage("El día "+$('#dia').val()+" no tienes ninguna guardia","error");
//			$("#obs_per").attr("disabled", "disabled").prev().addClass("disabled");		
		$('#hora').val(0);
    } else {
		// Vamos a poner la hora actual si tuene guardai en ella
		if (!getOptionSesion().hasAttribute('disabled'))
			$('#hora').val(sesion)
    }
    cambiaHora();
}

function setSesion() {
	sesion = Array.from(document.querySelectorAll('#hora option')).find(elem=>{
		var datosHora=elem.textContent.split("-");
		return (horaActual>=datosHora[0] && horaActual<=datosHora[1])
	}).value;
}

function getOptionSesion() {
	return Array.from(document.querySelectorAll('#hora option'))
		.find(elem=>elem.value==sesion);
}

function cambiaHora() {
    var token = $("#_token").text();
	// Borramos el formulario
	$("#hecha").prop("checked","");
	$("#obs").val("");
	$("#obs_per").val("");
	// Miramos si ese día a esa hora tiene guardia en ese lugar
	if (codLugar && sesion && dateEspToISO($('#dia').val())==diaHoy) {
		var correcto=guardiasSemana.some(item=>
			item.sesion_orden==$("#hora").val()
			&& item.dia_semana==dias_semana[new Date(dateEspToISO($('#dia').val())).getDay()]
			&& item.ocupacion==codLugar);
	}
	if (correcto) {
	// Si está en la biblioteca, hay hora elegido, es la hora actual y es el día de hoy
		$("#hecha").removeAttr("disabled").prev().removeClass("disabled");		
		$("#obs").removeAttr("disabled").prev().removeClass("disabled");		
		$("#hecha").prop("checked","checked");		// Marcamos por defecto que se hace
		$("#obs").focus();
	} else {
		$("#hecha").attr("disabled", "disabled").prev().addClass("disabled");
		$("#obs").attr("disabled", "disabled").prev().addClass("disabled");		
		$("#obs_per").focus();
	}

    // Pedimos la guardia al servidor
    if ($('#hora').val()>0) {
		$.ajax ({
	    	url: "api/guardia/idProfesor="+$('#dni').text()+"&dia="+dateEspToISO($('#dia').val())+"&hora="+$('#hora').val(),
//url: "api/guardia/idProfesor=021666373M&dia="+diaHoy+"&hora="+$('#hora option:selected').val(),
	    	type: "GET",
	    	dataType: "json",
                data: {api_token: token},
		}).then(function(res){
			idGuardia=0;
			for (let i in res.data) {
				// Si ha devuelto algo lo cargamos
				idGuardia=res.data[i].id;
				$("#obs").val(res.data[i].observaciones);
				$("#obs_per").val(res.data[i].obs_personal);
				if (res.data[i].realizada)
					$("#hecha").prop("checked","checked");
				else
					$("#hecha").prop("checked","");
				break;	// Ya la tenemos. No seguimos el bucle					
			}
		}, function(error){
			showMessage("Error "+error.status+": "+error.statusText, "error");
		})
	}
}

function modDatos(ev) {
	ev.preventDefault();
	if ($("#hora").val()=="0") {
		showMessage("Debes escoger la hora de la guardia", "error");
		return;
	}
	var datosJson={
		idProfesor: $('#dni').text(),
		dia: dateEspToISO($('#dia').val()), 
		hora: $("#hora").val(), 
		observaciones: $("#obs").val(), 
		obs_personal: $("#obs_per").val(),
		api_token : $("#_token").text()
	}
//	if (!$("#hecha").attr("disabled"))
	datosJson.realizada=($("#hecha").prop("checked")?1:0);

	if (idGuardia) {
		// Se modifica
		var tipo="PUT";
		var url='/api/guardia/'+idGuardia+'?api_token='+$("#_token").text();
	} else {
		// Se crea
		var tipo="POST";
		var url='/api/guardia?api_token='+$("#_token").text();
	}
    $.ajax({
		method: tipo,
		data: datosJson,
		url: url, 
		dataType: "json"})
    .then(function(res) {
		showMessage("La guardia se ha guardado correctamente", "ok");
	}, function(error){
		showMessage("Error "+error.status+": "+error.statusText, "error");
    });

}

function showMessage(msg, tipo) {
	$('.x_content').last().append(`
		<div id="msg" class="alert `+(tipo=='error'?`alert-danger `:`alert-success `)+`alert-block fade in">
			<button type="button" class="close" data-dismiss="alert">×</button>
			<p>
			    <strong>`+msg+`</strong>
		    </p>
		</div>
	`);
}

function dateEspToISO(date) {
	let arrFecha=date.split('-');
	arrFecha=arrFecha.map(dato=>(dato.length==1)?"0"+dato:dato);
	return(arrFecha[2]+'-'+arrFecha[1]+'-'+arrFecha[0]);
}

// fUENTE vieja: https://es.stackoverflow.com/questions/37404/obtener-ip-local-jquery
// Fuente: https://stackoverflow.com/questions/29959708/how-to-get-local-ip-address-in-javascript-html5?lq=1
/**
 * Get the user IP throught the webkitRTCPeerConnection
 * @param onNewIP {Function} listener function to expose the IP locally
 * @return undefined
 */
function getUserIP(onNewIP) { //  onNewIp - your listener function for new IPs
    //compatibility for firefox and chrome
    var myPeerConnection = window.RTCPeerConnection || window.mozRTCPeerConnection || window.webkitRTCPeerConnection;
    var pc = new myPeerConnection({
        iceServers: []
    }),
    noop = function() {},
    localIPs = {},
    ipRegex = /([0-9]{1,3}(\.[0-9]{1,3}){3}|[a-f0-9]{1,4}(:[a-f0-9]{1,4}){7})/g,
    key;

    function iterateIP(ip) {
        if (!localIPs[ip]) onNewIP(ip);
        localIPs[ip] = true;
    }

     //create a bogus data channel
    pc.createDataChannel("");

    // create offer and set local description
    pc.createOffer().then(function(sdp) {
        sdp.sdp.split('\n').forEach(function(line) {
            if (line.indexOf('candidate') < 0) return;
            line.match(ipRegex).forEach(iterateIP);
        });

        pc.setLocalDescription(sdp, noop, noop);
    }).catch(function(reason) {
        // An error occurred, so handle the failure to connect
    });

    //listen for candidate events
    pc.onicecandidate = function(ice) {
        if (!ice || !ice.candidate || !ice.candidate.candidate || !ice.candidate.candidate.match(ipRegex)) return;
        ice.candidate.candidate.match(ipRegex).forEach(iterateIP);
    };
}

function setPlace() {
	let infoIp = ipGuardia.find(item=>item.ip==miIP);
	if (infoIp) {
		biblio=true;
		codLugar = infoIp.codOcup;
		let descripLugar = ocupacionGuardia.find(item=>item.cod==infoIp.codOcup).descrip;
		document.querySelector('legend.centrado').textContent = 'Dades de la guàrdia de '+descripLugar;
	} else {
		codLugar = 1;
		document.querySelector('legend.centrado').textContent = 'Dades de la guàrdia';
	}
	cambiaHora();
}