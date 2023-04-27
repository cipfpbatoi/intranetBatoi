'use strict';
var id;
$(function() {
	$("#datatable").on("click", ".fa-unlock", function(event) {
		id=$(this).parents(".lineaGrupo").attr("id");
		event.preventDefault();
		$(this).attr("data-toggle","modal").attr("data-target", "#password").attr("href","");
	});
	$("#formPassword").on("submit", function(){
		$(this).attr("action","/reunion/"+id+"/deleteFile");
	});
	$('#datatable').on('draw.dt', function() {
		$(".fa-unlock").off("click").on("click", function(event) {
			id = $(this).closest("tr").attr("id");
			event.preventDefault();
			$(this).attr("data-toggle", "modal").attr("data-target", "#password").attr("href", "");
		});
	});

})
