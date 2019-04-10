'use strict';

$(function() {
    $("#anexoVI").on("click", function(event){
        event.preventDefault();
        $(this).attr("data-toggle","modal").attr("data-target", "#anexo").attr("href","");
    });
    $("#formAnexo").on("submit", function(){
        $(this).attr("action","/dual/anexeVI");
    });
})