$(function(){
    var $area = $('#area');
    var $content = $('#content');

    $area.attr('contenteditable', true);

    function syncContent() {
        $content.val($area.html());
    }

    $area.on('input keyup mouseup focusout paste', syncContent);
    $area.closest('form').on('submit', syncContent);
    syncContent();
})
