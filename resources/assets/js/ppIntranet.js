/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
'use strict';

import jQuery from 'jquery';
// Reutilitza jQuery global (amb plugins) si ja està carregat per Gentelella
const $ = window.jQuery || jQuery;
window.$ = window.jQuery = $;

$(function() {
	$(document).on('click', '[data-confirm]', function(event) {
		const message = $(this).data('confirm') || 'Segur que vols continuar?';
		if (!confirm(message)) {
			event.preventDefault();
			event.stopImmediatePropagation();
			return false;
		}
	});

	$(document).on('click', '[data-loading-text]', function() {
		const $btn = $(this);
		if ($btn.data('loading')) {
			return;
		}

		const loadingText = $btn.data('loading-text');
		if (!loadingText) {
			return;
		}

		$btn.data('loading', true);
		$btn.data('original-text', $btn.is('input') ? $btn.val() : $btn.text());

		if ($btn.is('input')) {
			$btn.val(loadingText);
			$btn.prop('disabled', true);
		} else {
			$btn.text(loadingText);
			$btn.attr('aria-disabled', 'true');
			$btn.addClass('disabled');
		}
	});

	$(".papelera").on('click', function(event) {
		if (!confirm('Vas a borrar el aviso de fecha '+$(this).next().find('span.time').text().trim()+':\n'+$(this).next().find('span.message').text().trim())) {
			event.preventDefault();
                }
	})
	// Mensaje de salida
	$('#imgFitxar').parents('a').on('click', function(event) {
		if (!confirm('Vas a fitxar que ixes del Centre i es va a tancar la Intranet')) {
			event.preventDefault();
		}

	})
	$("#question").on('click', function(event) {
		event.preventDefault();
		window.open(this.href,"Ajuda Intranet Batoi","width=520,height=600");
	})

})

document.addEventListener('DOMContentLoaded', function () {
	const fullBtn = document.querySelector('.glyphicon-fullscreen')?.parentElement;
	if (fullBtn) {
		fullBtn.addEventListener('click', function () {
			if (!document.fullscreenElement) {
				document.documentElement.requestFullscreen();
			} else {
				document.exitFullscreen();
			}
		});
	}
});
