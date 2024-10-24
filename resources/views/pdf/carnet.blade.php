@extends('layouts.pdf')
@section('content')
    @foreach ($todos as $elemento)
        @php
                $textColor = 'black';
            if ($elemento->departamento === 1) {
                $backgroundColor = 'grey'; // Anglés
                $textColor = 'white';
            } else if($elemento->departamento === 2) {
                $backgroundColor = 'purple'; // SC
                $textColor = 'white';
            } else if($elemento->departamento === 3) {
                $backgroundColor = 'lightpink'; // Imatge Personal
            }else if($elemento->departamento === 5) {
                $backgroundColor = 'orange'; // Admin
            }else if($elemento->departamento === 6) {
                $backgroundColor = 'lightblue'; // Sanitaria
            }else if($elemento->departamento === 10) {
                $backgroundColor = 'red'; // Hostaleria
                $textColor = 'white';
            }else if($elemento->departamento === 12 || $elemento->departamento === 18) {
                $backgroundColor = 'black'; // FOL
                $textColor = 'white';
            }else if($elemento->departamento === 24) {
                $backgroundColor = 'green'; // Informàtica
                $textColor = 'white';
            }else {
                $backgroundColor = 'yellow'; // PAS
            }
        @endphp
    <div class="page">
        <div class="container col-lg-12" style="margin-bottom: 0px;" >
            <div style="float:left;width:26%;margin-left:15px"><img src="{{public_path('/img/pdf/logo.png')}}" width="60px" height="60px" alt="Logo Insti"/></div>
            <div style="float:left;width:26%;margin-left:15px"><img style="margin: auto" src="{{public_path('img/pdf/conselleria.png')}} " width="130px" height="60px" alt="Logo Conselleria"/></div>
            <div style="float:left;width:26%;margin-left:50px"><img style="float:right" src="{{public_path('img/pdf/ue.png')}}" width="60px" height="60px" alt="Logo UE"/></div>
        </div>
        <div class="container col-lg-12 fondo"  style="border-top:{{ $backgroundColor }} 2px solid; margin-bottom: -1px; background-color: {{ $backgroundColor }}; background-image: url({{public_path('/img/pdf/insti.jpg')}});
        background-repeat: no-repeat;
        background-size: 120% auto;
        overflow: hidden;
        clear: both;">
            <div style="width: 100%; margin-bottom: 0px;">
                <p style="font-size: 10pt;text-align: center; margin-bottom: 1px;margin-top: 3px;"><strong >{!! config('contacto.nombre') !!}</strong></p>
                <strong style="padding-left: 2px; font-size: 9pt;">Nom : {!! $elemento->apellido1 !!} {!! $elemento->apellido2 !!}, {!! $elemento->nombre !!}</strong><br/>
                @if (isset($elemento->nia))
                    <strong style="padding-left: 2px; font-size: 8pt;">NIA : {!! $elemento->nia !!}</strong>
                @else
                    <strong style="padding-left: 2px; font-size: 8pt;">DNI : {!! $elemento->dni !!}</strong>
                @endif
            </div>
            <div style="width: 100%; margin-bottom: 0px ;">
                <div style="float:left;width:69%;">
                    <p style="padding-left: 2px; font-size: 6pt;  margin-top:40px;margin-bottom: 6px">{!! fullDireccion() !!}<br/>Telef: {!! config('contacto.telefono') !!}<br/>{!! config('contacto.web') !!}</p>
                    <div style="float:left;width:65%; text-align: center;margin-top: 10px; color: {{ $textColor }}">
                        <strong style="font-size: 9pt">{!!$datosInforme[1]!!}</strong>
                    </div>
                    <div style="float:left;width:35%;text-align: center; color: {{ $textColor }}">
                        <strong style="font-size: 7pt">Validesa </br>
                            {!! $datosInforme[0] !!}-{!! $datosInforme[0]+3 !!}</strong>
                    </div>
                </div>
                <div style="float:left;width:29%;margin-right: 5px;">
                    @if ($elemento->foto)
                        <img style="border:white solid thin;float:right;" src="{{public_path('/storage/fotos/'.$elemento->foto)}}" width="70px" height="90px" alt="Foto Carnet"/>
                    @else
                        <img style="border:white solid thin;float: right" src="" width="70px" height="90px" alt="Foto blanc" />
                    @endif
                </div>
            </div>

        </div>
    </div>
    @endforeach
@endsection
