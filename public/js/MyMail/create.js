$(function(){
    var $area = $('#area');
    var $content = $('#content');
    var $form = $area.closest('form');

    $area.attr('contenteditable', true);

    function syncContent() {
        $content.val($area.html());
    }

    $area.on('input keyup mouseup focusout paste', syncContent);
    $form.on('submit', syncContent);
    $form.find(':submit').on('click', syncContent);
    document.addEventListener('selectionchange', syncContent);
    syncContent();
})
