@extends('layouts.intranet')
@section('css')
<title>Enviar Avaluació Alumnes</title>
@endsection
@section('content')
<h3>Selecciona  nia de l'alumne per al que vos reenviar les dades d'avalació</h3>
<form method="POST" action='/sendAvaluacio'  >
    {{ csrf_field() }}
    <label>Nia Alumne:</label> <input type='text' id='nia' name='nia'/><br/>
    <input type='submit' value='Enviar'/>
</form>
<h3>Selecciona token per conèixer dades de l'usuari assignat</h3>
@if (isset($aR))
    <h5>Usuari:{{ $aR->Alumno->fullName }}</h5>
    <h5>Dni:{{ $aR->Alumno->dni }}</h5>
    <h5>Nia:{{ $aR->Alumno->nia }}</h5>
    <h5>Email:{{ $aR->Alumno->email }}</h5>
    <h5>Grupo:{{$aR->Reunion->grupoClase->nombre}}</h5>
    <h5>Promociona:{{($aR->capacitats==1)?'SI':'NO'}}</h5>
@endif
<form method="post" action="/getToken">
    {{ csrf_field() }}
    <label>Token:</label> <input type='text' id='token' name='token'/><br/>
    <input type='submit' value='Enviar'/>
</form>
@endsection
@section('titulo')
Enviar Avaluació Alumne
@endsection
@section('scripts')
@endsection
