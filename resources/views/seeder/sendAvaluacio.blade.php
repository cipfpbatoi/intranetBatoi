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
@endsection
@section('titulo')
Enviar Avaluació Alumne
@endsection
@section('scripts')
@endsection
