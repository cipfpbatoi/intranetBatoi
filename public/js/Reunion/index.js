'use strict';

var id;

$(function() {
	function showPasswordModal() {
		if (window.intranetUiHelpers && typeof window.intranetUiHelpers.showModal === 'function') {
			window.intranetUiHelpers.showModal('password');
			return;
		}

		$('#password').modal('show');
	}

	$('#datatable').on('click', '[id^="deleteFile"], .fa-unlock', function(event) {
		var unlockButton = $(this).closest('a');
		var row = unlockButton.closest('.lineaGrupo, tr');

		id = row.attr('id') || '';
		event.preventDefault();
		showPasswordModal();
	});

	$('#formPassword').on('submit', function() {
		$(this).attr('action', '/reunion/' + id + '/deleteFile');
	});
});
