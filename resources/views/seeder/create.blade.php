@extends('layouts.intranet')
@section('css')
<title>Importacio</title>
@endsection
@section('content')
<h3>Selecciona fitxer amb les dades</h3>
<h4>El procés tarda un temps, depenent de les dades a importar, NO s'ha d'interrompre !! Es recomana fer-ho en hores on no hi haja massa activitat.</h4>
<form id="formFichero" method="POST" action='/import' enctype="multipart/form-data" >
    {{ csrf_field() }}
    <label>Hi ha professors amb horari nou (no és substitut):</label> <input type='checkbox' id='primera' name='primera'/><br/>
    <label>Fitxer:</label><input type='file' id='fichero' name='fichero'/><br/>
    <input type='submit' class="submit" id="submit" value='Enviar'/>
</form>
@include('intranet.partials.modal.loading')
@endsection
@section('titulo')
Importació
@endsection
@section('scripts')
    {{ Html::script('/js/Import/create.js') }}
@endsection
