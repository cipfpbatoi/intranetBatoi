@extends('layouts.pdf')
@section('content')
@include('pdf.partials.cabecera')
<div class="container col-lg-12" >
    <table class="table table-bordered">
        <tr>
            <th>Sol·licitud Autorització Comissions de Serveis</th>
        </tr>
    </table>
</div>
<div class="container col-lg-12" >
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>FUNCIONARI/A:</th><th>DNI</th><th>Serveis</th><th>Exida</th><th>Tornada</th><th>Mitja de transport</th><th>Marca Vehicle</th><th>Matrícula</th><th>Kilometraje</th><th>Despeses</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($todos as $elemento)
            <tr>
                <td>{!! $elemento->Profesor->apellido1 !!} {!! $elemento->Profesor->apellido2 !!} {!! $elemento->Profesor->nombre !!}</td>
                <td>{!! $elemento->idProfesor !!}</td>
                <td>{{$elemento->descripcion}}</td>
                <td>{{$elemento->desde }}</td>
                <td>{{$elemento->hasta }}</td>
                <td>{{$elemento->tipoVehiculo }}</td>
                <td>{{$elemento->marca}}</td>
                <td>{{$elemento->matricula}}</td>
                <td>{{$elemento->kilometraje }}</td>
                <td>{{$elemento->total}}€</td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@include('pdf.partials.firmaGen',['title'=>'La direcció AUTORITZA el/s servei/s al/s lloc/s i durant el/s die/s i hor/es indicats amb el mitjà de locomoció assenyalat','signatura'=>'comision'])
@endsection




