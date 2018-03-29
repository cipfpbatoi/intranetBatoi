<div class="x_content">
<table id='datatable' class="table table-striped table-bordered display nowrap dtr-inline collapsed"  width='100%' data-page-length="150">
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
    </tr>
    </tfoot>
</table>
</div>


