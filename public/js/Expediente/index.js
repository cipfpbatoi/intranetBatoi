'use strict';

const MODEL="expediente";
var id;

$(function() {
	$(".refuse").on("click", function(event){
		event.preventDefault();
		$(this).attr("data-toggle","modal").attr("data-target", "#dialogo").attr("href","");
		id=$(this).parents(".profile_view").attr("id");
	});
	$("#formDialogo").on("submit", function(){
		$(this).attr("action",MODEL+"/"+id+"/refuse");
	});
	$("#dialogo").focus();
	$(".user").on("click",function(event){
		event.preventDefault();
		$(this).attr("data-toggle","modal").attr("data-target", "#select").attr("href","");
		id=$(this).parents(".profile_view").attr("id");
		var token = $("#_token").text();
	});
	$("#formSelect").on("submit", function(){
		$(this).attr("action",MODEL+"/"+id+"/assigna");
	});

})

