'use strict';
var id;
$(function() {
	$(".mensaje").on("click", function(event){
		event.preventDefault();
		$(this).attr("data-toggle","modal").attr("data-target", "#aviso").attr("href","");
		id=$(this).parents(".profile_view").attr("id");
	});
	$("#formAviso").on("submit", function(){
		$(this).attr("action","/profesor/"+id+"/mensaje");
	});
	$(".colectivo").on("click", function(event){
		event.preventDefault();
		$(this).attr("data-toggle","modal").attr("data-target", "#dialogo").attr("href","");
		id=$(this).parents(".profile_view").attr("id");
	});
	$("#formDialogo").on("submit", function(){
		$(this).attr("action","/profesor/colectivo");
	});
})
