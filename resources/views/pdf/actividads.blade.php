@extends('layouts.pdf')
@section('content')
@include('pdf.partials.cabecera')
<div class="container col-lg-12" >
    <table class="table table-bordered">
        <tr>
            <th>Full Registre d'Activitats extraescolars</th>
        </tr>
    </table>
</div>
<div class="container col-lg-12" >
    <table class="table table-bordered">
        <thead>
            <tr>
                <th style='width:2%'>N</th><th style='width:15%'>Activitat</th><th style='width:15%'>Descripció</th><th style='width:10%'>Objectius</th><th style='width:10%'>Desde</th><th style='width:10%'>Fins</th><th>Tipus</th><th style='width:10%'>Comentaris</th><th style='width:13%'>Profesors</th><th style='width:15%'>Grups</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($todos as $elemento)
            <tr>
                <td>{!! $elemento->id !!}</td>
                <td>{!! $elemento->name !!} </td>
                <td>{!! $elemento->descripcion !!}</td>
                <td>{!! $elemento->objetivos !!}</td>
                <td>{!! $elemento->desde !!}</td>
                <td>{!! $elemento->hasta !!}</td>
                @if (!$elemento->fueraCentro) <td>PROPI CENTRE</td>
                @else
                    @if ($elemento->transport) <td>AMB TRANSPORT</td>
                    @else <td>SENSE TRANSPORT</td>
                    @endif
                @endif

                </td>
                <td>{!! $elemento->comentarios !!}</td>
                <td>
                    @foreach ($elemento->profesores as $profesor)
                        {{$profesor->nombre}} {{$profesor->apellido1}}<br/>
                    @endforeach
                </td>
                <td>
                    @foreach ($elemento->grupos as $grupo)
                        {{$grupo->nombre}},<br/>
                    @endforeach
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<br/><br/><br/><br/><br/>
<p>La direcció AUTORITZA les activitats extraescolars durant el/s die/s i hor/es indicats</p>
<div class="container col-lg-12">
    <div style="width:50%;float:left">SIGNAT {{signatura('actividad')}}:</div>
    <div style="width:50%;float:right;text-align: right">{{strtoupper(config('contacto.poblacion'))}} A {{ $datosInforme }}</div>
</div>
@include('pdf.partials.pie',['document'=>'extraescolars'])
@endsection




