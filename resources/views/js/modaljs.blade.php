@include('js.tablesjs')
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
{{ Html::script("/js/barcode.js") }}
