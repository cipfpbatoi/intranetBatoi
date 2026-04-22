{{ Html::script("/js/common/api-auth.js", ['defer' => true]) }}
{{ Html::script("/js/common/ui-helpers.js", ['defer' => true]) }}
@if (file_exists('js/'.$panel->getModel().'/index.js'))
    {{ Html::script("/js/".$panel->getModel()."/index.js", ['defer' => true]) }}
@endif
{{ Html::script("/js/tabledit.js", ['defer' => true]) }}
{{ Html::script("/js/barcode.js", ['defer' => true]) }}
