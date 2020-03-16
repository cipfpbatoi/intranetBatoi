
    <div class="x_content">
        <h2>Resultats agregats</h2>
        <table style="border: #00aeef 1px solid">
            <thead>
            <tr>
                <td>Enquesta</td>
                @foreach ($options_numeric as $item) <th>{{$item->question}} </th> @endforeach
            </tr>
            </thead>
            <tr>
                <td>Tots</td>
                @foreach ($votes['all'] as $optionVote)
                    @if ($optionVote->sum('value')>0)
                        <td> {{round($optionVote->avg('value'),1)}} / {{$optionVote->count('value')}}</td>
                    @endif
                @endforeach
            </tr>
        </table>
    </div>
    <div class="x_content">
        <h2>Resultats per Grups</h2>
        <table style="border: #00aeef 1px solid">
            <thead>
            <tr>
                <td>Enquesta</td>
                @foreach ($options_numeric as $item) <th>{{$item->question}} </th> @endforeach
            </tr>
            </thead>
            <tr>
                @foreach ($votes['grup'] as $nameGroup => $grupVotes)
                @if ($grupVotes[1]->count())
                    <tr>
                        <td>{{\Intranet\Entities\Grupo::find($nameGroup)->nombre}}</td>
                        @foreach ($grupVotes as $optionVote)
                            <td> {{round($optionVote->avg('value'),1)}} / {{$optionVote->count('value')}} </td>
                        @endforeach
                    </tr>
                @endif
                @endforeach

            </tr>
        </table>
    </div>
    <div class="x_content">
        <h2>Resultats per Cicles</h2>
        <table style="border: #00aeef 1px solid">
            <thead>
            <tr>
                <td>Enquesta</td>
                @foreach ($options_numeric as $item) <th>{{$item->question}} </th> @endforeach
            </tr>
            </thead>
            <tr>
            @foreach ($votes['cicle'] as $nameGroup => $grupVotes)
                @if ($grupVotes[1]->count())
                <tr>
                    <td>{{\Intranet\Entities\Ciclo::find($nameGroup)->literal}} </td>
                    @foreach ($grupVotes as $optionVote)
                        <td> {{round($optionVote->avg('value'),1)}} / {{$optionVote->count('value')}} </td>
                    @endforeach
                </tr>
                @endif
            @endforeach
        </table>
    </div>
    <div class="x_content">
        <h2>Resultats per Departaments</h2>
        <table style="border: #00aeef 1px solid">
            <thead>
            <tr>
                <td>Enquesta</td>
                @foreach ($options_numeric as $item) <th>{{$item->question}} </th> @endforeach
            </tr>
            </thead>
            <tr>
            @foreach ($votes['departament'] as $nameGroup => $grupVotes)
                @if ($grupVotes[1]->count())
                    <tr>
                        <td>{{\Intranet\Entities\Departamento::find($nameGroup)->literal}}</td>
                        @foreach ($grupVotes as $optionVote)
                            <td> {{round($optionVote->avg('value'),1)}} / {{$optionVote->count('value')}} </td>
                        @endforeach
                    </tr>
                @endif
            @endforeach
        </table>
    </div>


