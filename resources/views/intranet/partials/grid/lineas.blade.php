@foreach ($panel->getElementos($pestana) as $elemento)
    <tr class="lineaGrupo {{$elemento->class??''}} " id='{{$elemento->getkey()}}'>
        @foreach ($pestana->getRejilla() as $item)
            @if (substr($item,0,1) == 'L')
                @php($long = 200)
            @else
                @php($long = 90)
            @endif
            <td>
                <span class='input' name='{{ $item }}'>
                    @if (isset($elemento->leido)&&!($elemento->leido))
                        <strong> {!!  $elemento->$item !!} </strong>
                    @else
                        {!!  eliminarTildes(mb_substr($elemento->$item, 0, $long))  !!}
                    @endif
                </span>
            </td>
        @endforeach
        <td>
            <span class="botones">
                @include('intranet.partials.components.buttons',['tipo'=>'grid'])
            </span>
        </td>
    </tr>
@endforeach
