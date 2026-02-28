'use strict';

var id;
const MODEL="comision";

$(function() {
	$(".refuse").on("click", function(event){
		event.preventDefault();
		$(this).attr("data-toggle","modal").attr("data-target", "#dialogo").attr("href","");
//		$("#formExplicacion").find("input[name=_token]").val(getToken());
		id=$(this).parents(".profile_view").attr("id");
	});
	$("#formDialogo").on("submit", function(){
		$(this).attr("action","/direccion/comision/"+id+"/refuse");
	});
	$(".paid").on("click", function(event) {
		// Comprovem si alguna casella de selecció amb la classe 'user' està seleccionada
		event.preventDefault();
		var legacyToken = $.trim($("#_token").text());
		var bearerToken = $.trim($('meta[name="user-bearer-token"]').attr('content') || "");
		var headers = {};
		if (bearerToken) {
			headers.Authorization = "Bearer " + bearerToken;
		}

		var selectedCheckboxes = $(".user:checked");

		if (selectedCheckboxes.length > 0) {

			var completedRequests = 0;

			selectedCheckboxes.each(function(i,checkbox) {
				var url = "/api/comision/"+checkbox.name+"/prePay";
				var requestData = {};
				if (!bearerToken && legacyToken) {
					requestData.api_token = legacyToken;
				}
				$.ajax({
					url: url, // Ajusta la URL de la teva API
					type: "PUT",
					data: requestData,
					headers: headers,
					success: function(data) {
						console.log("Petició PUT per " + checkbox.name + " enviada amb èxit.");
						// Incrementem el comptador de peticions completades
						completedRequests++;

						// Comprovem si totes les peticions s'han completat
						if (completedRequests === selectedCheckboxes.length) {
							// Totes les peticions han acabat, processa l'enllaç aquí
							console.log("Totes les peticions PUT han acabat, processant l'enllaç...");
							// Aquí pots posar el codi per processar l'enllaç
						} else {
							alert("Algunes peticions no ha sigut processades.");
						}
						$(location).attr('href', '/direccion/comision/paid');
					},
					error: function() {
						console.error("Hi ha hagut un error en l'enviament de la petició PUT per " + checkbox.name + ".");
						// Incrementem el comptador de peticions completades
						completedRequests++;

						// Comprovem si totes les peticions s'han completat
						if (completedRequests === selectedCheckboxes.length) {
							// Totes les peticions han acabat, processa l'enllaç aquí
							console.log("Totes les peticions PUT han acabat, processant l'enllaç...");
							// Aquí pots posar el codi per processar l'enllaç
						}
						alert("S'ha produit una errada en el processament.");
					}
				});
			});
		} else {
			alert("No hi ha cap casella de selecció seleccionada.");
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
