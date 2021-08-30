@php( $profesor = \Intranet\Entities\Profesor::find($todos->Creador()) )
@extends('layouts.pdf')
@section('content')
@include('pdf.partials.cabecera')
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
        <strong>Nom de l'activitat:</strong><br/>{{$todos->name}}<br/>
        <strong>Professor Responsable:</strong><br/>{{$profesor->fullName}}<br/>
        <strong>Departament:</strong><br/>{{$profesor->Departamento->vliteral}}<br/>
    </div>
    <div style="display: inline-block;width: 45%;float: right">
        <strong>Data de l'activitat:</strong><br/>{{$todos->desde}}<br/>
        <strong>Professors Participants:</strong><br/>
        @foreach ($todos->profesores as $teacher)
            {{$teacher->fullName}}<br/>
        @endforeach
        <strong>Cicle:</strong><br/>
        @foreach ($todos->grupos as $grupo)
            {{$grupo->Ciclo->vliteral}}<br/>
        @endforeach
    </div>
    <h2 style="text-decoration: underline ">Valoració de l'activitat</h2>
        <strong>Desenvolupament de l'activitat:</strong><br/>{{$todos->desenvolupament}}</br>
        <strong>Valoració pedagògica de l'activitat:</strong><br/>{{$todos->valoracio}}</br>
        <strong>Aspectes tranversals:</strong><br/>{{$todos->aspectes}}</br>
        <strong>Altres dades de l'activitat:</strong><br/>{{$todos->dades}}</br>
        <strong>Es recomana per al curs següent?</strong><br/>{{$todos->recomendada}}</br>
</div>
@include('pdf.partials.pie',['document'=>'valoracio'])
@endsection




