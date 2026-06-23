@php($colspan = $options_numeric->count() + 3)
<table>
    <thead>
    <tr>
        <th>Resultat Enquesta {{$poll->title}}</th>
        <th>Empreses del grup</th>
        <th>Valoracions totals</th>
        @foreach ($options_numeric as $item)
            <th>{{ $item->question_label }}</th>
        @endforeach
    </tr>
    </thead>
    <tbody>
    <tr>
        <th colspan="{{ $colspan }}">Resultats per Grups</th>
    </tr>
    @foreach ($votes['grup'] as $nameGroup => $grupVotes)
        @if ($hasVotes['grup'][$nameGroup] ?? false)
            @php($summary = $stats['grup_summary'][$nameGroup] ?? ['company_count' => 0, 'received_count' => 0])
            <tr>
                <td>{{ \Intranet\Entities\Grupo::find($nameGroup)->nombre }}</td>
                <td>{{ $summary['company_count'] }}</td>
                <td>{{ $summary['received_count'] }}</td>
                @foreach ($grupVotes as $optionId => $optionVote)
                    @php($avg = $stats['grup'][$nameGroup][$optionId]['avg'] ?? null)
                    <td>{{ $avg ?? '' }}</td>
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
            <th colspan="3">Resultats de selecció per Grups</th>
        </tr>
        @foreach ($select_stats['grup'] as $nameGroup => $groupStats)
            @if ($select_hasVotes['grup'][$nameGroup] ?? false)
                @foreach ($options_select as $item)
                    @foreach (($groupStats[$item->id] ?? []) as $choice => $count)
                        <tr>
                            <td>{{ \Intranet\Entities\Grupo::find($nameGroup)->nombre }} · {{ $item->question_label }}</td>
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
