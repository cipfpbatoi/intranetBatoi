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
    <th>@lang("validation.attributes.operaciones")</th>
</tr>
