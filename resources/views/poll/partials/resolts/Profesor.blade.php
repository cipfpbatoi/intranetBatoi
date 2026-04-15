@foreach ($myVotes as $nameModulo => $modulo)
    @foreach ($modulo as $codigo => $moduloVotes)
        @if ($moduloVotes->first())
            @php
                $allByOptions = $myGroupsVotes[$codigo]->groupBy('option_id');
            @endphp
            <h2>{{$nameModulo}} del grup {{$codigo}}</h2>
            <table style="border: #00aeef 1px solid">
                <thead>
                <tr>
                    <td>Enquesta</td>
                    @foreach ($options_numeric as $item) <th>{{$item->question}} </th> @endforeach
                </tr>
                </thead>
                <tr>
                    <td>Jo</td>
                    @foreach ($moduloVotes->groupBy('option_id') as $option)
                        @if ($option->sum('value')>0)
                            <td> {{round($option->avg('value'),1)}} de {{$option->count('value')}} Alumnes</td>
                        @endif
                    @endforeach
                </tr>
                <tr>
                    <td>Grup</td>
                    @foreach ($allByOptions as $option)
                        @if ($option->sum('value')>0)
                            <td>{{round($option->avg('value'),1)}} </td>
                        @endif
                    @endforeach
                </tr>
            </table>
            <h2>Comentaris:</h2>
            @foreach ($moduloVotes->whereIn('option_id',hazArray($options_text,'id')) as $votes)
                <p>{{$votes->text}}</p>
            @endforeach
            @if ($options_select->count())
                <h2>Opcions seleccionades:</h2>
                @foreach ($options_select as $selectOption)
                    @foreach ($moduloVotes->where('option_id', $selectOption->id) as $vote)
                        <p><strong>{{$selectOption->question}}:</strong> {{$vote->text}}</p>
                    @endforeach
                @endforeach
            @endif
        @endif
    @endforeach
@endforeach

