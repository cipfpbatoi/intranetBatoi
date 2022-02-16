'use strict';

var id;
const MODEL="falta";

$(function() {
	$(".refuse").on("click", function(event){
		event.preventDefault();
		$(this).attr("data-toggle","modal").attr("data-target", "#dialogo").attr("href","");
//		$("#formExplicacion").find("input[name=_token]").val(getToken());
		id=$(this).parents(".profile_view").attr("id");
	});
	$("#formDialogo").on("submit", function(){
		$(this).attr("action",MODEL+"/"+id+"/refuse");
	});
	$("#dialogo").focus();
})

function getToken() {
	var ppio=document.cookie.indexOf("XSRF-TOKEN=");
	if (ppio==-1)
		return "";
	else
		ppio+=11;	// para no coger el nombre de la cookie
	var fin=document.cookie.indexOf(";",ppio);
	if (fin==-1)
		fin=document.cookie.length;
	return document.cookie.substring(ppio, fin);
}
