@php($colspan = $options_numeric->count() + 1)
<table>
    <thead>
    <tr>
        <th>Resultat Enquesta {{$poll->title}}</th>
        @foreach ($options_numeric as $item)
            <th>{{ $item->question }}</th>
        @endforeach
    </tr>
    </thead>
    <tbody>
    <tr>
        <th colspan="{{ $colspan }}">Resultats per Grups</th>
    </tr>
    @foreach ($votes['grup'] as $nameGroup => $grupVotes)
        @if ($hasVotes['grup'][$nameGroup] ?? false)
            <tr>
                <td>{{ \Intranet\Entities\Grupo::find($nameGroup)->nombre }}</td>
                @foreach ($grupVotes as $optionId => $optionVote)
                    <td>
                        @if (($stats['grup'][$nameGroup][$optionId]['count'] ?? 0) > 0)
                            {{ $stats['grup'][$nameGroup][$optionId]['avg'] }} / {{ $stats['grup'][$nameGroup][$optionId]['count'] }}
                        @endif
                    </td>
                @endforeach
            </tr>
        @endif
    @endforeach
    </tbody>
</table>
