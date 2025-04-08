@props(['elemento', 'panel', 'pestana'])

<tr class="lineaGrupo {{ $elemento->class ?? '' }}" id="{{ $elemento->getKey() }}">
    @foreach ($pestana->getRejilla() as $item)
        @php($long = str_starts_with($item, 'L') ? 200 : 100)
        <td>
            <span class="input" name="{{ $item }}">
                @if (!empty($elemento->leido) && !$elemento->leido)
                    <strong>{!! in_substr($elemento->$item, $long) !!}</strong>
                @else
                    {!! in_substr($elemento->$item, $long) !!}
                @endif
            </span>
        </td>
    @endforeach
    <td>
        <span class="botones">
            <x-botones :panel="$panel" tipo="grid" :elemento="$elemento" /><br/>
        </span>
    </td>
</tr >