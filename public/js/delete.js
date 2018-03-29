$(function() {
	$(".fa-eraser").on('click', function(event) {
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
})
