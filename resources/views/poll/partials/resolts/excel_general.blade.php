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
        <th colspan="{{ $colspan }}">Resultats agregats</th>
    </tr>
    <tr>
        <td>Tots</td>
        @foreach ($votes['all'] as $optionId => $optionVote)
            <td>
                @if (($stats['all'][$optionId]['count'] ?? 0) > 0)
                    {{ $stats['all'][$optionId]['avg'] }} / {{ $stats['all'][$optionId]['count'] }}
                @endif
            </td>
        @endforeach
    </tr>

    <tr>
        <th colspan="{{ $colspan }}">Resultats per Cicles</th>
    </tr>
    @foreach ($votes['cicle'] as $nameGroup => $grupVotes)
        @if ($hasVotes['cicle'][$nameGroup] ?? false)
            <tr>
                <td>{{ \Intranet\Entities\Ciclo::find($nameGroup)->ciclo }}</td>
                @foreach ($grupVotes as $optionId => $optionVote)
                    <td>
                        @if (($stats['cicle'][$nameGroup][$optionId]['count'] ?? 0) > 0)
                            {{ $stats['cicle'][$nameGroup][$optionId]['avg'] }} / {{ $stats['cicle'][$nameGroup][$optionId]['count'] }}
                        @endif
                    </td>
                @endforeach
            </tr>
        @endif
    @endforeach
    </tbody>
</table>
