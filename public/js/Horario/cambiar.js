'use strict'

var esDireccion=false;
var profe;

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

window.onload=function() {
	esDireccion=($('#rol').text()%2 == 0);
	if (esDireccion){
		$('#guardar').text('Aprobar horario');
		profe=location.pathname.split('/')[2];
	}
	else profe= document.getElementById('dni').textContent;


	cargaCambios();
	$('#init').on('click', function(ev) {
		ev.preventDefault();
		var datos={
			estado: 'Pendiente',
			cambios: [],
			obs: ''
		};
		var auth = apiAuthOptions({data: JSON.stringify(datos)});
		$.ajax({
			type: "POST",
			url: "/api/horarioChange/"+profe,
			headers: auth.headers,
			data: auth.data,
			dataType: "json"
		}).then(function(res) {
			alert("Ja pots modificar l'horari");
			location.reload();
		}, function(err) {
			alert('Error al guardar los datos: '+err);
		});

	});

	$('#guardar').on('click', function(ev) {
		ev.preventDefault();
		var cambios=anotaCambios();
		var datos={
			estado: (esDireccion)?'Aceptado':'Pendiente',
			cambios: cambios,
			obs: $('#obs').val()
		};
		var auth = apiAuthOptions({data: JSON.stringify(datos)});
		$.ajax({
		    type: "POST",
		    url: "/api/horarioChange/"+profe,
			headers: auth.headers,
			data: auth.data,
		    dataType: "json"
		}).then(function(res) {
			if (esDireccion) {
				alert(res.data+'. Horario aprobado');
				location.reload();
			} else {
				alert(res.data+'. Tu nuevo horario te aparecerá cuando esté aprobado');
                location.href='/home';
			}
		}, function(err) {
			alert('Error al guardar los datos: '+err);
		});

	})
}

function dropHora(ev) {
	ev.preventDefault();
	if (this.children.length>0)	// NO permitimos dejar anda
		return false;
	try {
		var origen=$('#'+ev.originalEvent.dataTransfer.getData('text'));
	} catch(error) {
		console.error(error);
		return false;
	}
	if (!origen || !/^\d{1,2}-(L|M|X|J|V)$/.test(origen.attr('id')))
		return false;
	if (origen.attr('id')==this.id)
		return false;	// Suelto en la misma TD en que estoy
	var dato=origen.find('div');
	if (dato.attr('data-orig')==this.id)
		dato.removeClass('movido');
	else
		dato.addClass('movido');
	$(this).css('cursor','pointer').attr('draggable', 'true').on('dragstart', function(ev) {
   	    ev.originalEvent.dataTransfer.setData('text', this.id);
	}).append(dato);
	origen.css('cursor', 'default').attr('draggable', 'false').off('dragstart');
	this.removeEventListener('drop', dropHora);
	origen.on('drop', dropHora);
	// Y ocultamos el botón de 'Aplicar cambios'
	if (esDireccion)
		$('#aplicar').attr('hidden',true);
}

function anotaCambios() {
	var cambios=[];
	Array.from(document.querySelectorAll('tbody td')).forEach(function(tr) {
		var div=tr.getElementsByTagName('div')[0];
		if (div && div.getAttribute('data-orig') && tr.id && tr.id!=div.getAttribute('data-orig')) {
			cambios.push({de: div.getAttribute('data-orig'), a: tr.id});
		}
	})
	return cambios;
}

function cargaCambios() {
	//var profe= document.getElementById('dni').textContent;
	var auth = apiAuthOptions();
	$.ajax({
	    type: "GET",
	    url: "/api/horarioChange/"+profe,
		headers: auth.headers,
		data: auth.data,
	    dataType: "json"
	}).then(function(res) {
		var datos=JSON.parse(res.data);
		if (!datos.estado)
			datos.estado="Pendiente";
		var modificable=(datos.estado == 'Pendiente' || esDireccion);
		if (modificable)
			activaDragAndDrop();
		else {
			$('#guardar').attr('disabled', 'disabled');
			$('#obs').attr('disabled', 'disabled');
			alert('Tu horario ya ha sido aceptado por dirección y no lo puedes modificar');
		}
		realizaCambios(datos.cambios, modificable);
		$('#estado').val(datos.estado);
		$('#obs').val(datos.obs);
		if (datos.estado=="Aceptado" && esDireccion) {
		// Se puede cambiar ya el horario
			$('#aplicar').removeAttr('hidden');
			$('#aplicar').on('click', function() {
				if (confirm('Deseas aplicar ya estos cambios al horario del profesor?')) {
                                    location.href='/profesor/'+profe+'/horario-aceptar';
                                }
			})
                    }
	}, function(err) {
		if (err.responseJSON && err.responseJSON.message=="No hi ha fitxer") {
			var datos={
				estado: 'No hay propuesta',
				cambios: []
			};
			activaDragAndDrop();
			$('#estado').val(datos.estado);
			$('#obs').val('');
		 } else
			alert(err.responseText);
	});
}

function realizaCambios(cambios, modificable) {
	var datosOrig = [];

	cambios.forEach(cambio => {
		var celdaOrigen = document.getElementById(cambio.de);
		if (!celdaOrigen) {
			console.warn(`No s'ha trobat la cel·la d'origen amb id: ${cambio.de}`);
			return; // Salta aquest canvi si no existeix l'element origen
		}
		var dato = celdaOrigen.querySelector('div');
		if (!dato) {
			console.warn(`No s'ha trobat cap <div> dins de la cel·la d'origen amb id: ${cambio.de}`);
			return;
		}

		dato.classList.add('movido');
		datosOrig.push({ id: cambio.de, data: dato });
		var padre = dato.parentElement;
		if (modificable) {
			$(padre).css('cursor', 'default').attr('draggable', 'false').off('dragstart').on('drop', dropHora);
		}
		padre.removeChild(dato);
	});

	cambios.forEach(cambio => {
		var celda = document.getElementById(cambio.a);
		if (!celda) {
			console.warn(`No s'ha trobat la cel·la de destí amb id: ${cambio.a}`);
			return;
		}

		var dato = datosOrig.find(dato => dato.id == cambio.de)?.data;
		if (!dato) {
			console.warn(`No s'ha pogut trobar el <div> corresponent al canvi de ${cambio.de} a ${cambio.a}`);
			return;
		}

		celda.appendChild(dato);
		if (modificable) {
			celda.removeEventListener('drop', dropHora);
			celda.addEventListener('dragstart', function(ev) {
				ev.dataTransfer.setData('text', this.id);
			});
			$(celda).css('cursor', 'pointer').attr('draggable', 'true');
		}
		if (dato.getAttribute('data-orig') == celda.id) {
			dato.classList.remove('movido');
		} else {
			dato.classList.add('movido');
		}
	});
}


function activaDragAndDrop() {
	// Ponemos cursos y eventos draggable a las celdas ocupadas
	$('td.active').add('td.warning').css('cursor', 'pointer').attr('draggable', 'true').on('dragstart', function(ev) {
   	    ev.originalEvent.dataTransfer.setData('text', this.id);
	});
	// POnemos eventos drop a las celdas vacías
	$('table').find('tbody').find('td').on('dragover', function(ev) {
		ev.preventDefault()
	})
	$('table').find('td:empty').on('drop', dropHora);
}
