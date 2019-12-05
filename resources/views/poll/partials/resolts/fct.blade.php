<h2>Enquesta valoració de FCT del tutor</h2>
<table style="border: #00aeef 1px solid">
    <thead>
    <tr>
        <td>Empresa</td>
        @foreach ($options_numeric as $item) <th>{{$item->question}} </th> @endforeach
    </tr>
    </thead>
    @foreach (Intranet\Entities\Fct::misFcts()->get() as $fct)
    <tr>
        <td>{{$fct->centro}}</td>
        @foreach ($options_numeric as $item)
            <td>{{$myVotes[$fct->id][$item->id]}}</td>
        @endforeach
    </tr>
    @endforeach
</table>



