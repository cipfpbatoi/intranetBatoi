'use strict';
var id;
$(function() {
	$(".fa-unlock").on("click", function(event){
		id=$(this).parents(".lineaGrupo").attr("id");
		event.preventDefault();
		$(this).attr("data-toggle","modal").attr("data-target", "#password").attr("href","");
//		$("#formExplicacion").find("input[name=_token]").val(getToken());

	});
	$("#formPassword").on("submit", function(){
		$(this).attr("action","/reunion/"+id+"/deleteFile");
	});

})
