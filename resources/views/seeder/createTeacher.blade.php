@extends('layouts.intranet')
@section('css')
<title>Importacio</title>
@endsection
@section('content')
<h3>Selecciona fitxer amb les dades i dni del professor a importar</h3>
<form method="POST" action='/teacherImport' enctype="multipart/form-data" >
    {{ csrf_field() }}
    <label>Importa horaris:</label> <input type='checkbox' id='horari' name='horari'/><br/>
    <label>Recerca en Horaris Antics:</label><input type='checkbox' id='lost' name='lost'/><br/>
    <label>Professor a importar:</label> <input type='text' id='idProfesor' name='idProfesor'/><br/>
    <label>Fitxer:</label><input type='file' id='fichero' name='fichero'/><br/>
    <input type='submit' value='Enviar'/>
</form>
@endsection
@section('titulo')
Importació
@endsection
@section('scripts')
@endsection
