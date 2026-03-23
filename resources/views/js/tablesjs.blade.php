{{ Html::script("/js/common/api-auth.js") }}
{{ Html::script("/js/common/ui-helpers.js") }}
@if (file_exists('js/'.$panel->getModel().'/index.js'))
    {{ Html::script("/js/".$panel->getModel()."/index.js") }}
@endif
{{ Html::script("/js/tabledit.js") }}
{{ Html::script("/js/barcode.js") }}
