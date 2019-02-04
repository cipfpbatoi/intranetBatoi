@if ($todos->Alumnos->count() < 15)
    <ul style="font-size: normal;line-height: 1.5em">
            @foreach ($todos->Alumnos as $alumno)
                <li> <strong>{{$alumno->FullName}}</strong></li>
            @endforeach 
    </ul> 
@else
    <table style="width:100%;">
        <tr>
            @foreach ($todos->Alumnos as $index => $alumno)
            <td style="font-size: 18px;" ><strong><li> {{$alumno->ShortName}}</li></strong></td>
            @if ($index%3 == 2) <tr/><tr style="height: 0px;padding: 0px"> @endif
            @endforeach 
        </tr>
    </table>
@endif
