@extends('layouts.pdf')
@section('content')
@include('pdf.partials.cabecera')
<br/>
<div class="container col-lg-12" >
<table class="table table-bordered ">
    <tr>
        <th><h3>{{$datosInforme->Tipos()->vliteral}}</h3></th>
    </tr>
    <tr>
        <th><h4>Equip docent del grup "{{$datosInforme->Xgrupo}}" - Curs {{Curso()}}</h4></th>
    </tr>
</table>
</div>
<div class="container col-lg-12" >
    <p>Reunits els professors 
    @foreach ($datosInforme->profesores as $key => $profesor)
            @if ($profesor->pivot->asiste == 1)
                {{$profesor->nombre}} {{$profesor->apellido1}} {{$profesor->apellido2}},
            @endif
        @endforeach   
         membres de l'equip docent del grup "{{$datosInforme->Xgrupo}}" i havent analitzat les propostes presentades pels alumnes següents i que realitzaran en aquest curs i periode.
    </p>
    <p>
        Queden aprovades i s'ha fet l'assignament de tutories individuals per a cadascuna d'elles, quedant:
    </p>
</div>
<div class="container" >
    <ul style='list-style:none'>
        @foreach ($todos as $elemento)
        <li><strong>{{$elemento->descripcion}}</strong>:</li>
        <li class="ident">@php echo($elemento->resumen) @endphp</li>
        @endforeach    
    </ul>
</div>
<div class="container">
    <br/>
    <div style="width:60%;float:left">
    <strong>Signatura professors:</strong>
    <br/><br><br/>
    <ul style='list-style:none'>
        @foreach ($datosInforme->profesores as $profesor)
            @if ($profesor->pivot->asiste == 1)
            <li style="height: 50px">{{$profesor->nombre}} {{$profesor->apellido1}} {{$profesor->apellido2}}</li>
            @endif
        @endforeach    
    </ul>
    </div>
    <div style="width:60%;float:right">
        <p>TUTOR COL.LECTIU: {{$datosInforme->Responsable->nombre}}  {{$datosInforme->Responsable->apellido1}}  {{$datosInforme->Responsable->apellido2}}</p>
        <br/><br/>
        <p>{{strtoupper(config('contacto.poblacion'))}} A {{$datosInforme->hoy}}</p>
    </div>
   
        
</div>
@endsection
