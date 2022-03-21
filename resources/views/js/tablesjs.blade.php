@if (file_exists('js/'.$panel->getModel().'/index.js'))
    {{ Html::script("/js/".$panel->getModel()."/index.js") }}
@endif
{{ Html::script("/js/tabledit.js") }}
{{ Html::script("/js/barcode.js") }}
