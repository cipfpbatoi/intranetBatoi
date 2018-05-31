@extends('layouts.intranet')
@section('css')
<title>Esborrar Programacions</title>
@endsection
@section('content')
<h3>Vas a passar a la reserva les programacions caducades i esborrar les antigues.</h3>
<p>En són <strong>{{$cuantas}}</strong> i ho hauries de fer una vegada hagues introduit els horaris i així els professors poden consultar-les a l'inici del curs.
    El procés tarda un temps i NO s'ha d'interrompre !! </p>
<form method="POST" >
    {{ csrf_field() }}
    <input type='submit' value='Enviar'/>
</form>
@endsection
@section('titulo')
Esborrar Programacions
@endsection

