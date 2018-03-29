'use strict';

var id;
const MODEL="falta_itaca";

$(function() {
	$(".refuse").on("click", function(event){
		event.preventDefault();
		$(this).attr("data-toggle","modal").attr("data-target", "#dialogo").attr("href","");
//		$("#formExplicacion").find("input[name=_token]").val(getToken());
		id=$(this).parents(".profile_view").attr("id");
	});
	$("#formExplicacion").on("submit", function(){
		$(this).attr("action",MODEL+"/"+id+"/refuse");
	});
	$("#explicacion").focus();
})