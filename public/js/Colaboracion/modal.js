'use strict';
$(function () {
    $('.fa-plus').on("click", function(){
        var fct = this.id;
        $('#formFct').attr('action', '/fct/'+fct+'/alumnoCreate');
    });
});

