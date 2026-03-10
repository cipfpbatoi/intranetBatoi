/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
'use strict';

import jQuery from 'jquery';
import dt from 'datatables.net-bs4';
import dtButtons from 'datatables.net-buttons-bs4';
import dtResponsive from 'datatables.net-responsive-bs4';
import dtKeyTable from 'datatables.net-keytable-bs4';
import dtScroller from 'datatables.net-scroller-bs4';
import dtFixedHeader from 'datatables.net-fixedheader-bs4';

require('datatables.net-bs4/css/dataTables.bootstrap4.css');
require('datatables.net-buttons-bs4/css/buttons.bootstrap4.css');
require('datatables.net-responsive-bs4/css/responsive.bootstrap4.css');
require('datatables.net-keytable-bs4/css/keyTable.bootstrap4.css');
require('datatables.net-scroller-bs4/css/scroller.bootstrap4.css');
require('datatables.net-fixedheader-bs4/css/fixedHeader.bootstrap4.css');
// Reutilitza jQuery global (amb plugins) si ja està carregat per Gentelella
const $ = window.jQuery || jQuery;
window.$ = window.jQuery = $;

dt(window, $);
dtButtons(window, $);
dtResponsive(window, $);
dtKeyTable(window, $);
dtScroller(window, $);
dtFixedHeader(window, $);

window.JSZip = require('jszip');
const pdfMake = require('pdfmake/build/pdfmake');
const pdfFonts = require('pdfmake/build/vfs_fonts');
pdfMake.vfs = pdfFonts.pdfMake.vfs;
window.pdfMake = pdfMake;

require('datatables.net-buttons/js/buttons.html5.js');
require('datatables.net-buttons/js/buttons.print.js');

const createNoopDataTableRows = () => ({
	map: () => ({
		count: () => 0,
		toArray: () => [],
	}),
});

const createNoopDataTableApi = () => {
	const api = {
		order: () => api,
		draw: () => api,
		on: () => api,
		search: () => api,
		clear: () => api,
		destroy: () => api,
		buttons: () => api,
		ajax: () => ({
			url: () => ({
				load: () => api,
			}),
		}),
		columns: () => ({
			adjust: () => api,
		}),
		table: () => ({
			node: () => null,
		}),
		rows: () => ({
			data: () => createNoopDataTableRows(),
		}),
	};

	return api;
};

if (!$.fn.dataTable) {
	console.warn('DataTables no disponible: s\'utilitzen mètodes null object per evitar errors JS.');
	$.fn.dataTable = {
		isDataTable: () => false,
		moment: () => {},
	};
	$.fn.DataTable = () => createNoopDataTableApi();
}

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
	const fullBtn = document.querySelector('.fa-expand, .glyphicon-fullscreen')?.parentElement;
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
