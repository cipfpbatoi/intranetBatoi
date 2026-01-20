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
