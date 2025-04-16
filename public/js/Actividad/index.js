'use strict';

const MODEL="actividad";
var id;

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
	$("#explicacion").focus();
	$('#complementaria_id').change(function() {
		if ($(this).is(':checked')) {
			// Si està seleccionat, canvia el text del label a Justificacio_RA
			$('#field_descripcion_id label').text('Justificació RA');
			$('#descripcion_id').attr('placeholder', 'Justificació RA');
		} else {
			// Si no està seleccionat, canvia el text del label a Descripció d'activitat
			$('#field_descripcion_id label').text("Descripció d'activitat");
			$('#descripcion_id').attr('placeholder', 'Descripció d\'activitat');
		}
	});
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
