'use strict';

$(function() {
        var token = $("#_token").text();
	$('.del-notif').on('click', function() {
		// borrar notificación
		var id=$(this).parents('li').attr('id');
		$.ajax({
            method: "GET",
            url: "/notification/"+id+"/delete",
            data: {api_token: token},
        }).then(function (result) {
        	console.log(result);
		}, function(res) {
        	console.log(result);			
		})
	})
})