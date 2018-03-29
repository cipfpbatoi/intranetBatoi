@extends('layouts.pdf')
@section('content')
@include('pdf.partials.cabecera')
<table class="table table-bordered">
    <tr>
        <th>Sol.licitud Autorització Comissió de Serveis</th>
    </tr>
</table>

<div class="container col-lg-12" >
    <table class="table table-bordered">
        <tr >
            <td><strong>FUNCIONARI/A: </strong>{!! $todos->Profesor->apellido1 !!} {!! $todos->Profesor->apellido2 !!} {!! $todos->Profesor->nombre !!} </td>
            <td><strong>NIF: </strong> {!! $todos->idProfesor !!}</td>
        </tr>
    </table>
</div>
<div class="container" >
    <table class="table table-bordered table-condensed" style="font-size: small;">
        <tr><th>Serveis que ha realitzar</th><th>Eixida</th><th>Tornada</th><th colspan="3">Nombre de Dietes</th><th colspan="2">Locomoció</th></tr>
        <tr class="titol"><td  style="width:300px">OBJECTE-ITINERARI</td><td>DATA I HORA</td><td>DATA I HORA</td><td>ALLOTJAMENT</td><td>MENJAR</td><td>ALTRES DESPESES</td><td>MITJÀ</td><td>KM.</td></tr>
        <tr>
            <td><?php echo nl2br($todos->servicio);?></td>
            <td>{{$todos->salida }}</td>
            <td>{{$todos->entrada }}</td>
            <td>{{$todos->alojamiento }}</td>
            <td>{{$todos->comida }}</td>
            <td>{{$todos->otras }}</td>
            <td>{{$todos->medio }}</td>
            <td>{{$todos->kilometraje }}</td>
        </tr>
    </table>
</div>
<div class="container" >
    <table class="table table-bordered table-condensed" style="font-size: small;">
        <tr><th colspan="2" style="width:50%">Mitjà Locomoció Propi</th><th colspan="10">Altres Mitjos</th></tr>
        <tr><td  >Marca Vehicle: {{$todos->marca}}</td><td>Matrícula: {{$todos->matricula}}</td>
            <td>Avió</td><td>
            @if ($todos->otros == 0) X @endif
            </td>
            <td>Tren</td><td>
            @if ($todos->otros == 1) X @endif
            </td>
            <td>Taxi</td><td>
            @if ($todos->otros == 2) X @endif
            </td>
            <td>Autobús</td><td>
            @if ($todos->otros == 3) X @endif
            </td>
            <td>Altres</td><td>
            @if ($todos->otros == 4) X @endif
            </td>
        </tr>
    </table>
</div>
<div class="container">
    <div style="width:50%;float:left">SIGNAT EL/LA FUNCIONARIA:</div>
    <div style="width:50%;float:right;text-align: right">ALCOI A {{ $datosInforme }}</div>
</div>

<br/><br/><br/><br/><br/>
<p>La direcció AUTORITZA el/s servei/s al/s lloc/s i durant el/s die/s i hor/es indicats amb el mitjà de locomoció assenyalat</p>

@endsection
