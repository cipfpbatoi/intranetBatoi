<div class='centrado'>@include('intranet.partials.components.buttons',['tipo' => 'index'])</div><br/>
<div class="x_content">
<table id='dataFct' class="table table-striped" data-page-length="25">
    <thead>
    <tr>
        @foreach ($panel->getRejilla() as $item)
        <th>
            @if (strpos(trans("validation.attributes.$item"),'alidation.'))
            {{ucwords($item)}}
            @else    
            {{trans("validation.attributes.$item")}}
            @endif
        </th>
        @endforeach
        <th>@lang("validation.attributes.operaciones")</th>
    </tr>
    </thead>
    <tfoot>
    <tr>
        @foreach ($panel->getRejilla() as $item)
        <th>
            @if (strpos(trans("validation.attributes.$item"),'alidation.'))
            {{ucwords($item)}}
            @else    
            {{trans("validation.attributes.$item")}}
            @endif
        </th>
        @endforeach
        <th>@lang("validation.attributes.operaciones")</th>
    </tr>
    </tfoot>
</table>
</div>
