@extends('layouts.intranet')
@section('css')
<title>Canviar horaris</title>
@endsection
@section('content')
<h3>Vas a canviar els horaris aprovats</h3>
<p>Ho hauries de fer quan els professors han canviat els seus horaris i des de direcció s'han aprovat.
    Una vegada fet l'horari del profesor reflectirà els canvits fets !! </p>
<form method="POST" >
    {{ csrf_field() }}
    <input type='submit' value='Enviar'/>
</form>
@endsection
@section('titulo')
Canviar Horaris
@endsection

