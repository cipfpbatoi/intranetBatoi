<h2>Enquesta valoració de FCT del tutor</h2>
<table style="border: #00aeef 1px solid">
    <thead>
    <tr>
        <td>Enquesta</td>
        @foreach ($options as $item) <th>{{$item->question}} </th> @endforeach
    </tr>
    </thead>
    @foreach ($myVotes as $fct => $fctVotes)
        <tr>
            <td>{{Intranet\Entities\Fct::find($fct)->Colaboracion->Empresa}}</td>
            @foreach ($fctVotes as $option)

                <td> {{ $option }} </td>

            @endforeach
        </tr>
    @endforeach
</table>


