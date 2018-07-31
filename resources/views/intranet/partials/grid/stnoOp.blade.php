<div class="x_content">
    <table id='datatable' name='{{$panel->getModel()}}' class="table table-striped table-bordered display nowrap dtr-inline  "  width='100%' data-page-length="200" >
        <thead>
            <tr>
                @foreach ($pestana->getRejilla() as $item)
                <th>
                    @if (strpos(trans("validation.attributes.".trim($item,'X')),'alidation.'))
                    {{ucwords($item)}}
                    @else    
                    {{trans("validation.attributes.".trim($item,'X'))}}
                    @endif
                </th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            
            @foreach ($panel->getElementos($pestana) as $elemento)
            <tr class="lineaGrupo" id='{{$elemento->getkey()}}'>
                @foreach ($pestana->getRejilla() as $item)
                <td><span class='input' name='{{ $item }}'>@if (isset($elemento->leido)&&!($elemento->leido))<strong> {{ substr($elemento->$item,0,60) }} </strong> @else  {{ mb_substr($elemento->$item,0,80) }} @endif</span></td>
                @endforeach
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                @foreach ($pestana->getRejilla() as $item)
                <th>
                    @if (strpos(trans("validation.attributes.".trim($item,'X')),'alidation.'))
                    {{ucwords($item)}}
                    @else    
                    {{trans("validation.attributes.".trim($item,'X'))}}
                    @endif
                </th>
                @endforeach
            </tr>
        </tfoot>
    </table>
</div>