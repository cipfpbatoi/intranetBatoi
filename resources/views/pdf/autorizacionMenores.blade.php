@extends('layouts.pdf')
@section('content')
    @foreach ($todos as $elemento)
    <div class='page'>
        <div class="container col-lg-12" style="margin-bottom: 30px;" >
            <div style="float:left;width:30%">
                <img src="{{url('img/pdf/Conselleria-B_CON.jpg')}}" width="140px" height="70px"/><br/>
                <span style='font-size: xx-small'>
                {!! config('contacto.direccion') !!}<br/>    
                Tel - {!! config('contacto.telefono') !!} - Fax: {!! config('contacto.fax') !!}<br/>
                e-mail: {!! config('contacto.email') !!} <br/>
                {!! config('contacto.web') !!} <br/></span>
            </div>
            <div style="float:left;width:30%">
                <img src="{{url('img/pdf/cipfpbatoi.jpg')}} " width="180px" height="60px"/><br/>
            </div>
            <div style="float:left;width:30%;padding-left: 150px"><img src="{{url('img/pdf/FSEpos_val.jpg')}}" width="90px" height="90px"/></div>
        </div>
        <div class="container col-lg-12" >
            <p><strong>Asistencia a actividad extraescolar. Asistència a activitat extraescolar.</strong></p>
            <p>D/Dña __________________________________________________________ padre/madre o tutor/a de             
            {!! $elemento->nombre !!} {!! $elemento->apellido1 !!} {!! $elemento->apellido2 !!} con dni 
            {!! $elemento->dni !!} autorizo a que asista a la siguiente actividad extraescolar: </p>
            <p>En/Na __________________________________________________________ pare/mare o tutor/a de             
            {!! $elemento->nombre !!} {!! $elemento->apellido1 !!} {!! $elemento->apellido2 !!} amb dni 
            {!! $elemento->dni !!} autoritze a que assistisca a la següent activitat extraescolar: </p>
            <p><strong>{{ $datosInforme->name }}</strong></p>
            <p>Esta actividad se desarrollará desde {{ $datosInforme->desde }} hasta {{ $datosInforme->hasta }}.</p>
            <p>Esta activitat es desenvoluparà des de {{ $datosInforme->desde }} fins {{ $datosInforme->hasta }}.</p>
        </div>
        <br/><br/><br/><br/>
        <div class="container col-lg-6" style='padding-right: 200px;float:right' >
            <p>{!! config('contacto.poblacion') !!} a {{ FechaString() }}</p><br/><br/>
            <p>Firma / Signatura :</p>
        </div>
    </div>
    @endforeach
@endsection