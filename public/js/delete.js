$(function() {
	/*$(".fa-eraser").on('click', function(event) {
		let titles=$(this).parents('table').find('thead').find('th');
		let info="\n";
		$(this).parents('td').siblings().each(function(i, item) {
			if (item.innerHTML.trim().length>0) {
				info+=` - ${titles.eq(i).text().trim()}: ${item.firstElementChild.innerHTML}\n`;					
			}
		})
		if (!confirm('Vas a borrar el elemento:'+info)) {
			event.preventDefault();
		}
	})
	$(".fa-envelope").on('click', function(event) {
		let titles=$(this).parents('table').find('thead').find('th');
		let info="\n";
		$(this).parents('td').siblings().each(function(i, item) {
			if (item.innerHTML.trim().length>0) {
				info+=` - ${titles.eq(i).text().trim()}: ${item.firstElementChild.innerHTML}\n`;
			}
		})
		if (!confirm("Vas a tramitar l'element:"+info)) {
			event.preventDefault();
		}
	})*/
	$(".confirm").on('click',function(event){
		let info = $(this).text();
		if (!confirm('Confirma que vols realitzar la següent operació: '+info)) {
			event.preventDefault();
		}
	});
})


jQuery("#datatable").on("click",".fa-eraser" ,function () {
	let titles=$(this).parents('table').find('thead').find('th');
	let info="\n";
	$(this).parents('td').siblings().each(function(i, item) {
		if (item.innerHTML.trim().length>0) {
			info+=` - ${titles.eq(i).text().trim()}: ${item.firstElementChild.innerHTML}\n`;
		}
	})
	if (!confirm("Vas a esborrar l'element:"+info)) {
		event.preventDefault();
	}
})
	

jQuery("#datatable").on("click",".fa-envelope" ,function () {
	let titles=$(this).parents('table').find('thead').find('th');
	let info="\n";
	$(this).parents('td').siblings().each(function(i, item) {
		if (item.innerHTML.trim().length>0) {
			info+=` - ${titles.eq(i).text().trim()}: ${item.firstElementChild.innerHTML}\n`;
		}
	})
	if (!confirm("Vas a tramitar l'element:"+info)) {
		event.preventDefault();
	}
})
