@include('js.tablesjs')
{{ Html::script("/js/common/ui-helpers.js") }}
{{ Html::script("/js/common/api-auth.js") }}
{{ Html::script("/js/common/data-table.js") }}
@if (file_exists('js/'.$panel->getModel().'/grid.js'))
    {{ HTML::script('/js/'.$panel->getModel().'/grid.js') }}
@else
    {{ HTML::script('/js/grid.js') }}
@endif

@if (file_exists('js/'.$panel->getModel().'/modal.js'))
    {{ HTML::script('/js/'.$panel->getModel().'/modal.js') }}
@else
    @if (file_exists('js/'.$panel->getModel().'/create.js'))
        {{ HTML::script('/js/'.$panel->getModel().'/create.js') }}
    @endif
@endif
{{ HTML::script('/js/delete.js') }}
{{ HTML::script('/js/indexModal.js') }}
{{ Html::script("/js/datepicker.js") }}
