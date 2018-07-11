'use strict';
var id;
$(function() {
	$("#generar").on("click", function(event){
		event.preventDefault();
		$(this).attr("data-toggle","modal").attr("data-target", "#aviso").attr("href","");
		//id=$(this).parents(".profile_view").attr("id");
	});
	$("#formAviso").on("submit", function(){
		$(this).attr("action","/infdepartamento/create");
	});
})
