@extends('layouts.pdf')
@section('content')
@include('pdf.partials.cabecera')
<div class="container col-lg-12" >
    <table class="table table-bordered">
        <tr>
            <th>Full falta de marcatje birret per part del professorat</th>
        </tr>
    </table>
</div>
<div class="container col-lg-12" >
    <table class="table table-bordered" >
        <thead>
            <tr>
                <th>FUNCIONARI/A:</th><th>DNI</th><th>Dia</th><th>Hora</th><th>Grup</th><th>En centre</th><th>Justificació</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($todos as $elemento)
            <tr>
                <td>{!! $elemento->Profesor->apellido1 !!} {!! $elemento->Profesor->apellido2 !!} {!! $elemento->Profesor->nombre !!}</td>
                <td>{!! $elemento->idProfesor !!}</td>
                <td>{{$elemento->dia}}</td>
                <td>{{$elemento->Hora->hora_ini}} - {{$elemento->Hora->hora_fin}}</td>
                <td>{{$elemento->Grupo->nombre }}</td>
                <td>@if ($elemento->enCentro == 1) <span>-SI-</span> @else <span>-NO-</span>  @endif</td>
                <td>{{$elemento->justificacion }} </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@include('pdf.partials.firmaGen',['title'=>'La direcció MARCARÁ el birret el/s die/s i hor/es indicats','signatura'=>'birret'])
@endsection




