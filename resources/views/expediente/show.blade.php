@extends('layouts.intranet')
@section('content')
    <div class="col-md-6 col-sm-6 col-xs-12">
        <div class="x_panel">
            <div class="x_content">
                <div class="">
                    <ul class="to_do">
                        <li>Número Expedient: {{ $elemento->id }}</li>
                        <li>Mòdul: {{ $elemento->Modulo->literal??'' }} </li>
                        <li>Alumne: {{ $elemento->Alumno->fullName }} </li>
                        <li>E-mail Alumne: {{ $elemento->Alumno->email }} </li>
                        <li>Telèfon Alumne: {{ $elemento->Alumno->telef1}} - {{$elemento->Alumno->telef2}} </li>
                        <li>Profesor: {{ $elemento->Profesor->fullName }} </li>
                        <li>Data inici: {{ $elemento->fecha }} </li>
                        <li>Data fi: {{ $elemento->fechaSolucion }} </li>
                        <li>Explicació: {{ $elemento->explicacion }} </li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('titulo')
    Expediente nº {{$elemento->getKey()}}
@endsection

