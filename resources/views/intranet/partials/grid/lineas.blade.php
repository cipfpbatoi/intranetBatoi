@foreach ($panel->getElementos($pestana) as $elemento)
    <tr class="lineaGrupo {{$elemento->class??''}} " id='{{$elemento->getkey()}}'>
        @foreach ($pestana->getRejilla() as $item)
            @if (substr($item,0,1) == 'L')
                @php($long = 200)
            @else
                @php($long = 100)
            @endif
            <td>
                <span class='input' name='{{ $item }}'>
                    @if (isset($elemento->leido)&&!($elemento->leido))
                        <strong> {!!  in_substr($elemento->$item, $long) !!} </strong>
                    @else
                        {!!  in_substr($elemento->$item, $long)  !!}
                    @endif
                </span>
            </td>
        @endforeach
        <td>
            <span class="botones">
                 <x-botones :panel="$panel" tipo="grid" :elemento="$elemento ?? null"  /><br/>
            </span>
        </td>
    </tr>
@endforeach
