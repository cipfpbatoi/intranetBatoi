@php($grupo = authUser()?->Grupo?->first())
<h2>Enquesta alumnat</h2>
<table style="border: #00aeef 1px solid">
    <thead>
    <tr>
        <td>Enquesta</td>
        @foreach ($options as $item) <th>{{$item->question_label}} </th> @endforeach
    </tr>
    </thead>
    @foreach ($myVotes as $studentVotes)
        <tr>
            <td>{{ $grupo ? 'Grup '.$grupo->codigo : 'Resposta' }}</td>
            @foreach ($options as $item)
                <td>{{ $studentVotes[$item->id] ?? '' }}</td>
            @endforeach
        </tr>
    @endforeach
</table>
