@extends('layouts.intranet')
@section('css')
<title>Esborrar Programacions</title>
@endsection
@section('content')
<h3>Vas a esborrar les programacions caducades</h3>
<p>Aquelles que la data de fi és menor que hui. En tens <strong>{{$cuantas}}</strong> i ho hauries de fer quan els professors han tingut temps de consultar las de l'any anterior.
    El procés tarda un temps i NO s'ha d'interrompre !! </p>
<form method="POST" >
    {{ csrf_field() }}
    <input type='submit' value='Enviar'/>
</form>
@endsection
@section('titulo')
Esborrar Programacions
@endsection

