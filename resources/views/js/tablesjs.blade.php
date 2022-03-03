@if (file_exists('js/'.$panel->getModel().'/index.js'))
    {{ Html::script("/js/".$panel->getModel()."/index.js") }}
@endif
{{ Html::script("/js/tabledit.js") }}
<script src="https://code.jquery.com/ui/1.13.1/jquery-ui.js"></script>