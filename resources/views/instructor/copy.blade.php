@extends('layouts.intranet')
@section('css')
<title>{{trans("models.instructor.copy")}}</title>
@endsection
@section('content')
<h4>{{$instructor->nombre}} </h4>
<h6>{{$centro->nombre}}({{$centro->direccion}})</h6>
<h6>{{$instructor->email}}</h6>
<h6>{{$instructor->telefono}}</h6>
<form method="POST">
    {{ csrf_field() }}
    <label>Selecciona nou centre:</label>
        <select name='centro'/>
        @foreach ($posibles as $key => $value)
            <option value='{{$key}}'>{{$value}}</option>
        @endforeach    
        </select><br/>
    <label>Copia : </label> <input type='radio'  name='accion' value="copia"/><br/>
    <label>Mou a : </label> <input type='radio'  name='accion' value="mou"/><br/>
    <input type='submit' value='Enviar'/>
</form>
@endsection
@section('titulo')
{{trans("models.instructor.copy")}}
@endsection
