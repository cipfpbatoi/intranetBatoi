<div class="x_content">
    <table id='datatable' name='{{$panel->getModel()}}' class="table table-striped table-bordered display nowrap dtr-inline  "  width='100%' data-page-length="25" >
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
                <th>@lang("validation.attributes.operaciones")</th>
            </tr>
        </thead>
        <tbody>
            
            @foreach ($panel->getElementos($pestana) as $elemento)
            <tr class="lineaGrupo" id='{{$elemento->getkey()}}'>
                @foreach ($pestana->getRejilla() as $item)
                <td><span class='input' name='{{ $item }}'>@if (isset($elemento->leido)&&!($elemento->leido))<strong> {{ substr($elemento->$item,0,60) }} </strong> @else  {{ substr($elemento->$item,0,60) }} @endif</span></td>
                @endforeach
                <td><span class="botones">@include('intranet.partials.buttons',['tipo' => 'grid'])</span></td>
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
                <th>@lang("validation.attributes.operaciones")</th>
            </tr>
        </tfoot>
    </table>
</div>