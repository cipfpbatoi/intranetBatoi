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
        <td>Tots</td>
        @foreach ($votes['all'] as $optionVote)
            <td>{{ round($optionVote->avg('value'), 1) }} / {{ $optionVote->count('value') }}</td>
        @endforeach
    </tr>

    @foreach ($votes['cicle'] as $nameGroup => $grupVotes)
        <tr>
            <td>{{ \Intranet\Entities\Ciclo::find($nameGroup)->ciclo }}</td>
            @foreach ($grupVotes as $optionVote)
                <td>{{ round($optionVote->avg('value'), 1) }} / {{ $optionVote->count('value') }}</td>
            @endforeach
        </tr>
    @endforeach

    @foreach ($votes['departament'] as $nameGroup => $grupVotes)
        <tr>
            <td>{{ \Intranet\Entities\Departamento::find($nameGroup)->literal }}</td>
            @foreach ($grupVotes as $optionVote)
                <td>{{ round($optionVote->avg('value'), 1) }} / {{ $optionVote->count('value') }}</td>
            @endforeach
        </tr>
    @endforeach
    </tbody>
</table>
