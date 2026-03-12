@include('js.tablesjs')
{{ Html::script("/js/common/ui-helpers.js") }}
@if (file_exists('js/'.$panel->getModel().'/grid.js'))
{{ HTML::script('/js/'.$panel->getModel().'/grid.js') }}
@else
{{ HTML::script('/js/grid.js') }}
@endif
{{ HTML::script('/js/delete.js') }}
