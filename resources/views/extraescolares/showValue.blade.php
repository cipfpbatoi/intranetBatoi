@extends('layouts.intranet')
@php( $profesor = \Intranet\Entities\Profesor::find($Actividad->Creador()) )
@section('css')
<title></title>
@endsection
@section('content')
<h4 class="centrado">{{trans("models.Actividad.titulo",['actividad'=>$Actividad->name])}}</h4>
    <div class="container"  >
        <table class="table table-bordered" style="width: 100%">
            <tr>
                <th><h2>Memòria Activitat Complementària/Extraescolar</h2></th>
            </tr>
        </table>
    </div>
    <div class="container" >
        <h2 style="text-decoration: underline">Descripció de l'activitat</h2>
        <div style="display: inline-block;width: 45%">
            <strong>Nom de l'activitat:</strong><br/>{{$Actividad->name}}<br/>
            <strong>Professor Responsable:</strong><br/>{{$profesor->fullName}}<br/>
            <strong>Departament:</strong><br/>{{$profesor->Departamento->vliteral}}<br/>
        </div>
        <div style="display: inline-block;width: 45%;float: right">
            <strong>Data de l'activitat:</strong><br/>{{$Actividad->desde}}<br/>
            <strong>Professors Participants:</strong><br/>
            @foreach ($Actividad->profesores as $teacher)
                {{$teacher->fullName}}<br/>
            @endforeach
            <strong>Cicle:</strong><br/>
            @foreach ($Actividad->grupos as $grupo)
                {{$grupo->Ciclo->vliteral}}<br/>
            @endforeach
        </div>
        <h2 style="text-decoration: underline ">Valoració de l'activitat</h2>
        <div style="display: inline-block;width: 45%">
            <strong>Desenvolupament de l'activitat:</strong><br/>{{$Actividad->desenvolupament}}</br>
            <strong>Valoració pedagògica de l'activitat:</strong><br/>{{$Actividad->valoracio}}</br>
            <strong>Aspectes tranversals:</strong><br/>{{$Actividad->aspectes}}</br>
        </div>
        <div style="display: inline-block;width: 45%;float: right">
            <strong>Altres dades de l'activitat:</strong><br/>{{$Actividad->dades}}</br>
            <strong>Es recomana per al curs següent?</strong><br/>{{$Actividad->recomendada}}</br>
        </div>
    </div>
    <br />
<a href="/actividad" class="btn btn-info" >@lang('messages.buttons.volver')</a>
@endsection
