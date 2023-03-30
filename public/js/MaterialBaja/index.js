'use strict';

jQuery("#datatable").on("click",".fa-remove" ,function () {
    let titles=$(this).parents('table').find('thead').find('th');
    let info="\n";
    $(this).parents('td').siblings().each(function(i, item) {
        if (item.innerHTML.trim().length>0) {
            info+=` - ${titles.eq(i).text().trim()}: ${item.firstElementChild.innerHTML}\n`;
        }
    })
    if (!confirm("Vas a rebutjar la baixa de l'element:"+info)) {
        event.preventDefault();
    }
})