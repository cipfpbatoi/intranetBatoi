@props(['elemento', 'panel', 'pestana'])

<tr class="lineaGrupo {{ $elemento->class ?? '' }}" id="{{ $elemento->getKey() }}">
    @foreach ($pestana->getRejilla() as $item)
        @php($long = str_starts_with($item, 'L') ? 200 : 100)
        @php($teHtmlControlat = $item === 'FullName' && isset($elemento->incidenciaFullName))
        @php($valor = $teHtmlControlat ? $elemento->incidenciaFullName : rescue(fn() => $elemento->$item, ''))
        @php($valorRetallat = $teHtmlControlat ? $valor : in_substr($valor, $long))
        <td>
            <span class="input" name="{{ $item }}">
                @if (!empty($elemento->leido) && !$elemento->leido)
                    <strong>{!! $teHtmlControlat ? $valorRetallat : e($valorRetallat) !!}</strong>
                @else
                    {!! $teHtmlControlat ? $valorRetallat : e($valorRetallat) !!}
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
