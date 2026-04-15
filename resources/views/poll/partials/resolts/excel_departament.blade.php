@php($colspan = $options_numeric->count() + 1)
<table>
    <thead>
    <tr>
        <th>Resultat Enquesta {{$poll->title}}</th>
        @foreach ($options_numeric as $item)
            <th>{{ $item->question_label }}</th>
        @endforeach
    </tr>
    </thead>
    <tbody>
    <tr>
        <th colspan="{{ $colspan }}">Resultats per Departaments</th>
    </tr>
    @foreach ($votes['departament'] as $nameGroup => $grupVotes)
        @if ($hasVotes['departament'][$nameGroup] ?? false)
            <tr>
                <td>{{ \Intranet\Entities\Departamento::find($nameGroup)->literal }}</td>
                @foreach ($grupVotes as $optionId => $optionVote)
                    <td>
                        @if (($stats['departament'][$nameGroup][$optionId]['count'] ?? 0) > 0)
                            {{ $stats['departament'][$nameGroup][$optionId]['avg'] }} / {{ $stats['departament'][$nameGroup][$optionId]['count'] }}
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
            <th colspan="3">Resultats de selecció per Departaments</th>
        </tr>
        @foreach ($select_stats['departament'] as $nameGroup => $groupStats)
            @if ($select_hasVotes['departament'][$nameGroup] ?? false)
                @foreach ($options_select as $item)
                    @foreach (($groupStats[$item->id] ?? []) as $choice => $count)
                        <tr>
                            <td>{{ \Intranet\Entities\Departamento::find($nameGroup)->literal }} · {{ $item->question_label }}</td>
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
