@extends('layouts.intranet')
@php( $profesor = \Intranet\Entities\Profesor::find($Actividad->Creador()) )
@section('css')
<title></title>
@endsection
@section('content')
    <div class="container"  >
        <table class="table table-bordered" style="width: 100%">
            <tr>
                <th><h2>Memòria Activitat Complementària/Extraescolar {{$Actividad->name}}</h2></th>
            </tr>
        </table>
    </div>
    <div class="container" >
        <h2> <em style="text-decoration: underline">Descripció de l'activitat</em> </h2>
        <div style="display: inline-block;width: 20%">
            <strong>Data de l'activitat:</strong><br/>{{$Actividad->desde}}<br/>
            <strong>Professors Participants:</strong><br/>
            <ul class="list-unstyled">
                @foreach ($Actividad->profesores as $profesor)
                    <li><em class="fa fa-user"></em>
                        @if($profesor->pivot->coordinador)
                            <strong>{{$profesor->nombre}} {{$profesor->apellido1}}</strong>
                        @else
                            {{$profesor->nombre}} {{$profesor->apellido1}}
                        @endif
                    </li>
                @endforeach
            </ul>
        </div>
        <div style="display: inline-block;width: 50%;margin-right:30px;float: right">
            <strong>Objectius:</strong><br/><em style="font-size: smaller">{{$Actividad->objetivos}}</em><br/>
            <strong>Comentaris:</strong><br/><em style="font-size: smaller">{{$Actividad->comentarios}}</em><br/>
        </div>
        <div style="display: inline-block;width: 25%;float: right">
            <strong>Departament:</strong><br/>
            {{$profesor->Departamento?$profesor->Departamento->vliteral:'Desconegut'}}
            <br/>
            <strong>Grups Participants:</strong><br/>
            <ul class="list-unstyled">
                @foreach ($Actividad->grupos as $grupo)
                    <li><em class="fa fa-group"></em> {{ $grupo->nombre}} </li>
                @endforeach
            </ul>
        </div>
        <hr/>
        <h2 style="text-decoration: underline ">Valoració de l'activitat</h2>
        <div style="display: inline-block;width: 45%">
            <strong>Desenvolupament de l'activitat:</strong><br/><em style="font-size: smaller">{{$Actividad->desenvolupament}}</em><br>
            <strong>Valoració pedagògica de l'activitat:</strong><br/><em style="font-size: smaller">{{$Actividad->valoracio}}</em><br>
            <strong>Aspectes tranversals:</strong><br/><em style="font-size: smaller">{{$Actividad->aspectes}}</em><br>
        </div>
        <div style="display: inline-block;width: 45%;float: right">
            <strong>Altres dades de l'activitat:</strong><br/><em style="font-size: smaller">{{$Actividad->dades}}</em><br>
            <strong>Es recomana per al curs següent?</strong><br/><em style="font-size: smaller">{{$Actividad->recomendada}}</em><br>
        </div>
    </div>
    <br />
@endsection
