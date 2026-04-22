@include('js.tablesjs')
{{ Html::script("/js/common/ui-helpers.js", ['defer' => true]) }}
{{ Html::script("/js/common/data-table.js", ['defer' => true]) }}
@if (file_exists('js/'.$panel->getModel().'/grid.js'))
{{ HTML::script('/js/'.$panel->getModel().'/grid.js', ['defer' => true]) }}
@else
{{ HTML::script('/js/grid.js', ['defer' => true]) }}
@endif
{{ HTML::script('/js/delete.js', ['defer' => true]) }}
