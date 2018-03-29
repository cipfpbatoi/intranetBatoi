@extends('layouts.intranet')
@section('css')
<title>Importacio</title>
@endsection
@section('content')
<h3>Selecciona fitxer amb les dades</h3>
<h4>El procés tarda un temps, depenent de les dades a importar, NO s'ha d'interrompre !! Es recomana fer-ho en hores on no hi haja massa activitat.</h4>
<form method="POST" action='/import' enctype="multipart/form-data" >
    {{ csrf_field() }}
    <label>Asigna tutors:</label> <input type='checkbox' id='tutores' name='tutores'/><br/>
    <label>Dar de baja grupos sin tutor:</label><input type='checkbox' id='tutores' name='bajaGrupo'/><br/>
    <label>Fitxer:</label><input type='file' id='fichero' name='fichero'/><br/>
    <input type='submit' value='Enviar'/>
</form>
@endsection
@section('titulo')
Importació
@endsection
@section('scripts')
@endsection
