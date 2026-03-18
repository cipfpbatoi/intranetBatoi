/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */
'use strict';

import jQuery from 'jquery';
import DataTable from 'datatables.net/js/dataTables.mjs';
import 'datatables.net-bs5/js/dataTables.bootstrap5.mjs';
import 'datatables.net-buttons/js/dataTables.buttons.mjs';
import 'datatables.net-buttons-bs5/js/buttons.bootstrap5.mjs';
import 'datatables.net-responsive/js/dataTables.responsive.mjs';
import 'datatables.net-responsive-bs5/js/responsive.bootstrap5.mjs';
import 'datatables.net-keytable/js/dataTables.keyTable.mjs';
import 'datatables.net-keytable-bs5/js/keyTable.bootstrap5.mjs';
import 'datatables.net-scroller/js/dataTables.scroller.mjs';
import 'datatables.net-scroller-bs5/js/scroller.bootstrap5.mjs';
import 'datatables.net-fixedheader/js/dataTables.fixedHeader.mjs';
import 'datatables.net-fixedheader-bs5/js/fixedHeader.bootstrap5.mjs';

import 'datatables.net-bs5/css/dataTables.bootstrap5.css';
import 'datatables.net-buttons-bs5/css/buttons.bootstrap5.css';
import 'datatables.net-responsive-bs5/css/responsive.bootstrap5.css';
import 'datatables.net-keytable-bs5/css/keyTable.bootstrap5.css';
import 'datatables.net-scroller-bs5/css/scroller.bootstrap5.css';
import 'datatables.net-fixedheader-bs5/css/fixedHeader.bootstrap5.css';
import 'datatables.net-buttons/js/buttons.html5.mjs';
import 'datatables.net-buttons/js/buttons.print.mjs';
import JSZip from 'jszip';
import pdfMake from 'pdfmake/build/pdfmake';
import pdfFonts from 'pdfmake/build/vfs_fonts';
// Reutilitza jQuery global (amb plugins) si ja està carregat per Gentelella
const $ = window.jQuery || jQuery;
window.$ = window.jQuery = $;
window.DataTable = window.DataTable || DataTable;

window.JSZip = JSZip;
if (typeof pdfMake.addVirtualFileSystem === 'function') {
	pdfMake.addVirtualFileSystem(pdfFonts);
} else if (pdfFonts && pdfFonts.pdfMake && pdfFonts.pdfMake.vfs) {
	pdfMake.vfs = pdfFonts.pdfMake.vfs;
}
window.pdfMake = pdfMake;

const intranetRuntime = window.IntranetRuntime || (window.IntranetRuntime = {});
intranetRuntime.isProduction = intranetRuntime.isProduction ?? (
	(document.body && document.body.dataset && document.body.dataset.appEnv === 'production') ||
	(document.documentElement && document.documentElement.dataset && document.documentElement.dataset.appEnv === 'production')
);

const intranetWarn = (...args) => {
	if (!intranetRuntime.isProduction && window.console && typeof window.console.warn === 'function') {
		window.console.warn(...args);
	}
};

const resolveLegacyFeatures = () => {
	const rawFeatures = (document.body && document.body.dataset && document.body.dataset.legacyFeatures) || '';
	if (!rawFeatures) {
		return null;
	}

	return new Set(
		rawFeatures
			.split(',')
			.map((feature) => feature.trim())
			.filter(Boolean)
	);
};

const hasLegacyFeature = (featureSet, featureName) => !featureSet || featureSet.has(featureName);

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
	intranetWarn('DataTables no disponible: s\'utilitzen mètodes null object per evitar errors JS.');
	$.fn.dataTable = {
		isDataTable: () => false,
		moment: () => {},
	};
	$.fn.DataTable = () => createNoopDataTableApi();
}

if (window.__INTRANET_PPINTRANET_INITIALIZED__) {
	intranetWarn('ppIntranet.js ja estava inicialitzat; s\'evita registrar handlers duplicats.');
} else {
	window.__INTRANET_PPINTRANET_INITIALIZED__ = true;

	$(function() {
		const legacyFeatures = resolveLegacyFeatures();

		if (hasLegacyFeature(legacyFeatures, 'confirm')) {
			$(document).on('click', '[data-confirm]', function(event) {
				const message = $(this).data('confirm') || 'Segur que vols continuar?';
				if (!confirm(message)) {
					event.preventDefault();
					event.stopImmediatePropagation();
					return false;
				}
			});
		}

		if (hasLegacyFeature(legacyFeatures, 'loading-text')) {
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
		}

		if (hasLegacyFeature(legacyFeatures, 'paperera') && $('.papelera').length) {
			$('.papelera').on('click', function(event) {
				if (!confirm('Vas a borrar el aviso de fecha ' + $(this).next().find('span.time').text().trim() + ':\n' + $(this).next().find('span.message').text().trim())) {
					event.preventDefault();
				}
			});
		}

		// Mensaje de salida
		if (hasLegacyFeature(legacyFeatures, 'fitxar') && $('#imgFitxar').length) {
			$('#imgFitxar').parents('a').on('click', function(event) {
				if (!confirm('Vas a fitxar que ixes del Centre i es va a tancar la Intranet')) {
					event.preventDefault();
				}
			});
		}

		if (hasLegacyFeature(legacyFeatures, 'help-popup') && $('#question').length) {
			$('#question').on('click', function(event) {
				event.preventDefault();
				window.open(this.href, 'Ajuda Intranet Batoi', 'width=520,height=600');
			});
		}
	});

	document.addEventListener('DOMContentLoaded', function() {
		const legacyFeatures = resolveLegacyFeatures();
		if (!hasLegacyFeature(legacyFeatures, 'fullscreen')) {
			return;
		}

		const fullBtn = document.querySelector('.fa-expand, .glyphicon-fullscreen')?.parentElement;
		if (fullBtn) {
			fullBtn.addEventListener('click', function() {
				if (!document.fullscreenElement) {
					document.documentElement.requestFullscreen();
				} else {
					document.exitFullscreen();
				}
			});
		}
	});
}
