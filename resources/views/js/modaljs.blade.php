@include('js.tablesjs')
{{ Html::script("/js/common/ui-helpers.js", ['defer' => true]) }}
{{ Html::script("/js/common/api-auth.js", ['defer' => true]) }}
{{ Html::script("/js/common/data-table.js", ['defer' => true]) }}
@if (file_exists('js/'.$panel->getModel().'/grid.js'))
    {{ HTML::script('/js/'.$panel->getModel().'/grid.js', ['defer' => true]) }}
@else
    {{ HTML::script('/js/grid.js', ['defer' => true]) }}
@endif

@if (file_exists('js/'.$panel->getModel().'/modal.js'))
    {{ HTML::script('/js/'.$panel->getModel().'/modal.js', ['defer' => true]) }}
@else
    @if (file_exists('js/'.$panel->getModel().'/create.js'))
        {{ HTML::script('/js/'.$panel->getModel().'/create.js', ['defer' => true]) }}
    @endif
@endif
{{ HTML::script('/js/delete.js', ['defer' => true]) }}
{{ HTML::script('/js/indexModal.js', ['defer' => true]) }}
{{ Html::script("/js/datepicker.js", ['defer' => true]) }}
