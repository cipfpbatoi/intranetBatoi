'use strict'

var esDireccion=false;

window.onload=function() {
	esDireccion=($('#rol').text()%2 == 0);
	if (esDireccion)
		$('#guardar').text('Aprobar horario');
	cargaCambios();
	$('#guardar').on('click', function(ev) {
		var cambios=anotaCambios(); 
		var datos={
			estado: (esDireccion)?'Aceptado':'Pendiente',
			cambios: cambios,
			obs: $('#obs').val()
		}
		var profe=location.pathname.split('/')[2];
		$.ajax({
		    type: "POST",
		    url: "/api/horarioChange/"+profe+'?api_token='+$("#_token").text()+'&data='+JSON.stringify(datos),
		    contentType: "application/json",
		    dataType: "json"
		}).then(function(res) {
			if (esDireccion) {
				alert(res.data+'. Horario aprobado');				
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
	var profe=location.pathname.split('/')[2];
	$.ajax({
	    type: "GET",
	    url: "/api/horarioChange/"+profe+'?api_token='+$("#_token").text(),
	    contentType: "application/json",
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
	// guardamos los datos que machacaremos antes de machacarlos con los nuevos datos
	// por si hay un cambio cruzado, ej. 1-L a 1-M y 1-M a 1-L
	var datosOrig=[];
	cambios.forEach(cambio=>{
		var dato=document.getElementById(cambio.de).querySelector('div');
		dato.classList.add('movido');
		datosOrig.push({id: cambio.de, data: dato});
		var padre=dato.parentElement;
		if (modificable)
			$(padre).css('cursor', 'default').attr('draggable', 'false').off('dragstart').on('drop', dropHora);
		padre.removeChild(dato);
	})
	// guardamos los nuevos cambios
	cambios.forEach(cambio=>{
		var celda=document.getElementById(cambio.a);
		var dato=datosOrig.find(dato=>dato.id==cambio.de).data;
		celda.appendChild(dato);
		if (modificable) {
			celda.removeEventListener('drop', dropHora);
			celda.addEventListener('dragstart', function(ev) {
	     	    ev.dataTransfer.setData('text', this.id);			
			})
			$(celda).css('cursor','pointer').attr('draggable', 'true');			
		}
		if (dato.getAttribute('data-orig')==celda.id)
			dato.classList.remove('movido');
		else
			dato.classList.add('movido');
	})
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
