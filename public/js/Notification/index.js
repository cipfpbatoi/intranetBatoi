'use strict';

$(function() {
	function apiAuthOptions(extraData) {
		var legacyToken = $.trim($("#_token").text());
		var bearerToken = $.trim($('meta[name="user-bearer-token"]').attr('content') || "");
		var data = extraData || {};
		var headers = {};

		if (bearerToken) {
			headers.Authorization = "Bearer " + bearerToken;
		} else if (legacyToken) {
			data.api_token = legacyToken;
		}

		return { headers: headers, data: data };
	}

	$('.del-notif').on('click', function() {
		// borrar notificación
		var id=$(this).parents('li').attr('id');
		var auth = apiAuthOptions();
		$.ajax({
            method: "GET",
            url: "/notification/"+id+"/delete",
            headers: auth.headers,
            data: auth.data,
        }).then(function (result) {
        	console.log(result);
		}, function(res) {
        	console.log(res);			
		})
	})
})

$(document).ready(function() {
	var table = $('#datatable').DataTable();
	table.order([0, 'dsc']).draw();
});
