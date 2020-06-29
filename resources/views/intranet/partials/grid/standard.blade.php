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
                @if ($panel->countBotones('grid'))
                    <th>@lang("validation.attributes.operaciones")</th>
                @endif
            </tr>
        </thead>
        <tbody>
            
            @foreach ($panel->getElementos($pestana) as $elemento)
            <tr class="lineaGrupo" id='{{$elemento->getkey()}}'>
                @foreach ($pestana->getRejilla() as $item)
                    @if (substr($item,0,1) == 'L') @php $long = 200; @endphp
                    @else @php $long = 80; @endphp @endif    
                <td><span class='input' name='{{ $item }}'>@if (isset($elemento->leido)&&!($elemento->leido))<strong> {{ substr($elemento->$item,0,$long) }} </strong> @else  {{ mb_substr($elemento->$item,0,$long) }} @endif</span></td>
                @endforeach
                @if ($panel->countBotones('grid'))
                <td><span class="botones">
                        @foreach ($panel->getBotones('grid') as $button)
                            @if (isset($elemento) && $button!='')
                                {{ $button->show($elemento) }}
                            @else
                                {{ $button->show() }}
                            @endif
                        @endforeach
                </span></td>
                @endif
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
                 @if ($panel->countBotones('grid'))
                    <th>@lang("validation.attributes.operaciones")</th>
                 @endif
            </tr>
        </tfoot>
    </table>
</div>