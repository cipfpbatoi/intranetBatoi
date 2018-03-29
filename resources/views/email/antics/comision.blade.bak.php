@extends('layouts.email')
@section('content')
<table style='text-align: center'>
    <tr>
        <th>Sol.licitud Autorització Comissió de Serveis</th>
    </tr>
</table>

<div>
    <table style=" border:#000 solid 1;">
        <tr >
            <td><strong>FUNCIONARI/A: </strong>{!! $elemento->Profesor->apellido1 !!} {!! $elemento->Profesor->apellido2 !!} {!! $elemento->Profesor->nombre !!} </td>
            <td><strong>NIF: </strong> {!! $elemento->idProfesor !!}</td>
        </tr>
    </table>
</div>
<div class="container" >
    <table class="table-bordered" style="font-size: small;">
        <tr><th>Serveis que ha realitzar</th><th>Eixida</th><th>Tornada</th></tr>
        <tr>
            <td><?php echo nl2br($elemento->servicio);?></td>
            <td>{{$elemento->salida }}</td>
            <td>{{$elemento->entrada }}</td>
        </tr>
    </table>
</div>
<a href="{{ url("profesor/".$modelo."/".$elemento->id."/authorize") }}" >Autoritzar</a>
@endsection