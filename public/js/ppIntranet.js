/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
'use strict';

$(function() {
	$(".papelera").on('click', function(event) {
		if (!confirm('Vas a borrar el aviso de fecha '+$(this).next().find('span.time').text().trim()+':\n'+$(this).next().find('span.message').text().trim())) {
			event.preventDefault();
                }
	})
	// Mensaje de salida
	$('#imgFitxar').parents('a').on('click', function(event) {
		if (!confirm('Vas a fitxar que ixes del Centre i es va a tancar la Intranet')) {

		}

	})
	$("#question").on('click', function(event) {
		event.preventDefault();
		window.open(this.href,"Ajuda Intranet Batoi","width=520,height=600");
	})

})

