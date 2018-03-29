'use strict';
var id;
$(function() {
	$(".mensaje").on("click", function(event){
		event.preventDefault();
		$(this).attr("data-toggle","modal").attr("data-target", "#aviso").attr("href","");
//		$("#formExplicacion").find("input[name=_token]").val(getToken());
		id=$(this).parents(".profile_view").attr("id");
	});
	$("#formAviso").on("submit", function(){
		$(this).attr("action","/profesor/"+id+"/mensaje");
	});
        $(".colectivo").on("click", function(event){
		event.preventDefault();
		$(this).attr("data-toggle","modal").attr("data-target", "#dialogo").attr("href","");
//		$("#formExplicacion").find("input[name=_token]").val(getToken());
		id=$(this).parents(".profile_view").attr("id");
	});
	$("#formExplicacion").on("submit", function(){
		$(this).attr("action","/profesor/colectivo");
	});
})
