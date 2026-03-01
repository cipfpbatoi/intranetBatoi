'use strict'

const MaxDiasAtras=7;	// nº de días que puede cambiar atrás
const ocupacionGuardia=3249454;

var sesion=0;		// value del tramo horario en que estamos
var guardiasSemana=[];	// datos de las guardias del profesor
var idGuardia=0;	// indica si ya se ha registrado esta guardia
var ipGuardia = ''
var biblio=false;
var diaHoy="";
var diaSelec="";
var horaActual="";
var dias_semana=["D", "L", "M", "X", "J", "V", "S"];

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

$(function() {
    $.ajax ({
    	url: "/api/ipGuardias",
    	type: "GET",
    	dataType: "json",
		headers: apiAuthOptions().headers,
        data: apiAuthOptions().data
        }).then(function(res){
            ipGuardia = res.data;
        });    
        
	//Test: Print the IP addresses into the console
	getIPs(function(ip) {
		if (ipGuardia.includes(ip)) {
                	biblio=true;
			cambiaHora();
		}
    });
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
    	url: "/api/horario/idProfesor="+$('#dni').text()+"&ocupacion="+ocupacionGuardia,//&dia_semana="+dias_semana[ahora.getDay()],
//url: "api/horario/idProfesor=021666373M&ocupacion=3249454&dia_semana="+dias_semana[ahora.getDay()],
    	type: "GET",
    	dataType: "json",
		headers: apiAuthOptions().headers,
        data: apiAuthOptions().data,
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
    var horas=guardiasSemana.filter(guardia=>guardia.dia_semana==dias_semana[fecha.getDay()]);
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
	var auth = apiAuthOptions();
	// Borramos el formulario
	$("#hecha").prop("checked","");
	$("#obs").val("");
	$("#obs_per").val("");
	// Miramos si el día es hoy
	if (biblio && sesion && $("#hora").val() == sesion && dateEspToISO($('#dia').val())==diaHoy) {
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
            headers: auth.headers,
            data: auth.data,
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
		obs_personal: $("#obs_per").val()
	}
	var auth = apiAuthOptions();
	if (!auth.headers.Authorization && auth.data.api_token) {
		datosJson.api_token = auth.data.api_token;
	}
//	if (!$("#hecha").attr("disabled"))
	datosJson.realizada=($("#hecha").prop("checked")?1:0);

	if (idGuardia) {
		// Se modifica
		var tipo="PUT";
		var url='/api/guardia/'+idGuardia;
	} else {
		// Se crea
		var tipo="POST";
		var url='/api/guardia';
	}
    $.ajax({
		method: tipo,
		data: datosJson,
		headers: auth.headers,
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

// fUENTE: https://es.stackoverflow.com/questions/37404/obtener-ip-local-jquery
function getIPs(callback){
    var ip_dups = {};

    //compatibility for firefox and chrome
    var RTCPeerConnection = window.RTCPeerConnection
        || window.mozRTCPeerConnection
        || window.webkitRTCPeerConnection;
    var useWebKit = !!window.webkitRTCPeerConnection;

    //bypass naive webrtc blocking using an iframe
    if(!RTCPeerConnection){
        //NOTE: you need to have an iframe in the page right above the script tag
        //
        //<iframe id="iframe" sandbox="allow-same-origin" style="display: none"></iframe>
        //<script>...getIPs called in here...
        //
        var win = iframe.contentWindow;
        RTCPeerConnection = win.RTCPeerConnection
            || win.mozRTCPeerConnection
            || win.webkitRTCPeerConnection;
        useWebKit = !!win.webkitRTCPeerConnection;
    }

    //minimal requirements for data connection
    var mediaConstraints = {
        optional: [{RtpDataChannels: true}]
    };

    var servers = {iceServers: [{urls: "stun:stun.services.mozilla.com"}]};

    //construct a new RTCPeerConnection
    var pc = new RTCPeerConnection(servers, mediaConstraints);

    function handleCandidate(candidate)
    {
        //match just the IP address
        var ip_regex = /([0-9]{1,3}(\.[0-9]{1,3}){3}|[a-f0-9]{1,4}(:[a-f0-9]{1,4}){7})/
        var ip_addr = ip_regex.exec(candidate)[1];

        //remove duplicates
        if(ip_dups[ip_addr] === undefined)
            callback(ip_addr);

        ip_dups[ip_addr] = true;
    }

    //listen for candidate events
    pc.onicecandidate = function(ice){

        //skip non-candidate events
        if(ice.candidate)
            handleCandidate(ice.candidate.candidate);
    };

    //create a bogus data channel
    pc.createDataChannel("");

    //create an offer sdp
    pc.createOffer(function(result){

        //trigger the stun server request
        pc.setLocalDescription(result, function(){}, function(){});

    }, function(){});

    //wait for a while to let everything done
    setTimeout(function(){
        //read candidate info from local description
        var lines = pc.localDescription.sdp.split('\n');


        lines.forEach(function(line){
            if(line.indexOf('a=candidate:') === 0)
            {
              handleCandidate(line);
            }

        });
    }, 1000);
}
