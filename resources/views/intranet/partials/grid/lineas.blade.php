@foreach ($panel->getElementos($pestana) as $elemento)
    <tr class="lineaGrupo {{$elemento->class??''}} " id='{{$elemento->getkey()}}'>
        @foreach ($pestana->getRejilla() as $item)
            @if (substr($item,0,1) == 'L') @php $long = 200; @endphp
            @else @php $long = 90; @endphp @endif
            <td><span class='input' name='{{ $item }}'>@if (isset($elemento->leido)&&!($elemento->leido))<strong> {!!  substr($elemento->$item,0,$long) !!} </strong> @else  {!!  mb_substr($elemento->$item,0,$long)  !!} @endif</span></td>
        @endforeach
        <td>
            <span class="botones">
                @include('intranet.partials.components.buttons',['tipo'=>'grid'])
            </span>
        </td>
    </tr>
@endforeach
