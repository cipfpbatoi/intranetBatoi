@extends('layouts.intranet')
@section('css')
<title>Importacio</title>
@endsection
@section('content')
<h3>Selecciona fitxer importar</h3>
<form method="POST" action='/importEmail' enctype="multipart/form-data" >
    {{ csrf_field() }}
    <label>Fitxer:</label><input type='file' id='fichero' name='fichero'/><br/>
    <input type='submit' value='Enviar'/>
</form>
<p>El fitxer ha de tindre l'extensió .csv</p>
<p>Si és professor el format serà : dni;email_corporatiu</p>
<p>Si és alumne serà: nia;email_corporatiu</p>
@endsection
@section('titulo')
Importació
@endsection
@section('scripts')
@endsection
