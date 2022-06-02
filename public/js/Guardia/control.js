'use strict'

var dias=['L','M','X','J','V'];
var fecha=new Date();
cambiaFecha(1-fecha.getDay());

$(function() {
	// Guardamos la tabla original
	const tablaOrig=$('#table-guardias').find('tbody').children();
	// Pedimos las guardias
	loadGuardias();
	$('.next-week').on('click', function(e) {
		e.preventDefault();
		cambiaFecha(+7, tablaOrig);
	})
	$('.prev-week').on('click', function(e) {
		e.preventDefault();
		cambiaFecha(-7, tablaOrig);
	})
})

function cambiaFecha(dias, tablaOrig) {
	fecha.setDate(fecha.getDate()+dias);
	$('#hora-title').children().nextAll().each(function(i) {
		var queFecha=new Date(fecha);
		queFecha.setDate(queFecha.getDate()+i);
		this.innerHTML=getFecha(queFecha);
	});
	if (tablaOrig) {
//		$('#table-guardias').find('tbody').replaceWith(tablaOrig);
		borraTabla(tablaOrig);
		loadGuardias();
	}
}

function borraTabla(tablaOrig) {
	// $('#table-guardias').find('tbody')
	// 	.empty()
	// 	.append(tablaOrig);
	$('#table-guardias').find('tbody').find('div').each(function(i) {
		$(this).children().first().addClass('label label-danger');
		$(this).children().nextAll().remove();
	})
}
function loadGuardias() {
	let fechaFin=new Date(fecha);
	fechaFin.setDate(fecha.getDate()+4);
	$.ajax ({
		url: "/api/guardia/dia]"+getFecha(fecha)+"&dia["+getFecha(fechaFin),
    	type: "GET",
    	dataType: "json",
    	data: {
    		api_token: $("#_token").text()
    	}
	}).then(function(res){
	    // Pintamos los datos
	    for (var i in res.data) {
	    	let guardia=res.data[i];
	    	let indexDia=(new Date(guardia.dia)).getDay();
	    	let divProfe=$('#hora-'+guardia.hora).children().eq(indexDia)
	    		.find("div[data-dni="+guardia.idProfesor+"]")
	    	if (guardia.realizada === 1) {
	    		divProfe.children()[0].className='label label-default';
	    	} else if (guardia.observaciones || guardia.obs_personal) {
	    		divProfe.children()[0].className='label label-warning';
	    	} else {
				divProfe.children()[0].className='label label-danger';
			}
	    	if (guardia.obs_personal) {
	    		divProfe.append('<br><span>Nota: '+guardia.obs_personal+'</span>')
	    	}
	    	if (guardia.observaciones) {
	    		divProfe.append('<br><span>OBS: '+guardia.observaciones+'</span>')
	    	}
	    }
	}, function(error){
		console.error("Error "+error.status+": "+error.statusText, "error");
	});
}

function getFecha(fecha) {
	return fecha.toISOString().split('T')[0];
}
