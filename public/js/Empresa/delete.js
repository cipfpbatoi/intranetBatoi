$(function() {
	$(".fa-trash").on('click', function(event) {
		let info=$(this).parents('li').find('.info').text();
                if ($(this).parents('ul').hasClass('colaboracion')) 
                    opcion = 'la colaboración entre';
                else
                    if ($(this).parents('ul').hasClass('fct'))
                        opcion = 'la fct de ';
                    else    
                        if ($(this).parent('a').hasClass('instructor')){
                            opcion = 'el instructor';
                            info = $(this).parents('h4.text-info').find('.nombre').text();
                        }
                        else    
                           opcion = 'el centro de trabajo';
		if (!confirm('Vas a borrar '+opcion+': '+info)) {
			event.preventDefault();
		}
	})
        $("#Borrar").on('click', function(event) {
		if (!confirm('!! Vas a borrar la empresa, los centros y todas sus colaboraciones !!')) {
			event.preventDefault();
		}
	})
})
