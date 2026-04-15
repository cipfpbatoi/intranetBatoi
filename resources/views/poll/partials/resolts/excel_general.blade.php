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

@if ($options_select->count())
    <table>
        <thead>
        <tr>
            <th>Resultat Enquesta {{$poll->title}}</th>
            <th>Opció</th>
            <th>Recompte</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <th colspan="3">Resultats de selecció</th>
        </tr>
        @foreach ($options_select as $item)
            <tr>
                <th colspan="3">{{ $item->question }}</th>
            </tr>
            @foreach (($select_stats['all'][$item->id] ?? []) as $choice => $count)
                <tr>
                    <td>Tots</td>
                    <td>{{ $choice }}</td>
                    <td>{{ $count }}</td>
                </tr>
            @endforeach
        @endforeach

        <tr>
            <th colspan="3">Resultats de selecció per Cicles</th>
        </tr>
        @foreach ($select_stats['cicle'] as $nameGroup => $groupStats)
            @if ($select_hasVotes['cicle'][$nameGroup] ?? false)
                @foreach ($options_select as $item)
                    @foreach (($groupStats[$item->id] ?? []) as $choice => $count)
                        <tr>
                            <td>{{ \Intranet\Entities\Ciclo::find($nameGroup)->ciclo }} · {{ $item->question }}</td>
                            <td>{{ $choice }}</td>
                            <td>{{ $count }}</td>
                        </tr>
                    @endforeach
                @endforeach
            @endif
        @endforeach
        </tbody>
    </table>
@endif
