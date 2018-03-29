<div class="x_content">
<table id='datatable' class="table table-striped table-bordered display nowrap dtr-inline collapsed"  width='100%' data-page-length="25">
    <thead>
    <tr>
        @foreach ($panel->getRejilla() as $item)
        <th>
            @if (strpos(trans("validation.attributes.".trim($item,'X')),'alidation.'))
                {{ucwords($item)}}
            @else    
                {{trans("validation.attributes.".trim($item,'X'))}}
            @endif
        </th>
        @endforeach
        <th>{{trans("validation.attributes.operaciones")}}</th>
    </tr>
    </thead>
    <tfoot>
    <tr>
        @foreach ($panel->getRejilla() as $item)
        <th>
            @if (strpos(trans("validation.attributes.".trim($item,'X')),'alidation.'))
                {{ucwords($item)}}
            @else    
                {{trans("validation.attributes.".trim($item,'X'))}}
            @endif
        </th>
        @endforeach
        <th>{{trans("validation.attributes.operaciones")}}</th>
    </tr>
    </tfoot>
</table>
</div>


