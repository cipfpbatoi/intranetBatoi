$(function(){
    $('#area').focusout(function() {
        $('#content').val($('#area').html());
    });
})