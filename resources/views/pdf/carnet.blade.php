@extends('layouts.pdf')
@section('content')
    @foreach ($todos as $elemento)
    <div class="page" >
        <div class="container col-lg-12" style="margin-bottom: 0px;" >
            <div style="float:left;width:26%;margin-left:15px"><img src="{{public_path('img/pdf/logo.png')}}" width="80px" height="40px" alt="Logo Insti"/></div>
            <div style="float:left;width:26%;margin-left:15px"><img style="margin: auto" src="{{public_path('img/pdf/conselleria.png')}} " width="140px" height="45px" alt="Logo Conselleria"/></div>
            <div style="float:left;width:26%;margin-left:50px"><img style="float:right" src="{{public_path('img/pdf/ue.png')}}" width="60px" height="60px" alt="Logo UE"/></div>
        </div>
        <div class="container col-lg-12 fondo"  style="background-image: url({{public_path('/img/pdf/insti.jpg')}});
        background-repeat: no-repeat;
        background-size: 120% auto;
        overflow: hidden;
        clear: both;">
            <div style="width: 100%; margin-bottom: 0px ;">
                <p style="font-size: 10pt;text-align: center; margin-bottom: 1px;margin-top: 3px;"><strong >{!! config('contacto.nombre') !!}</strong></p>
                <strong style="font-size: 9pt;">Nom : {!! $elemento->apellido1 !!} {!! $elemento->apellido2 !!}, {!! $elemento->nombre !!}</strong><br/>
                @if (isset($elemento->nia))
                    <strong style="font-size: 8pt;">NIA : {!! $elemento->nia !!}</strong>
                @else
                    <strong style="font-size: 8pt;">DNI : {!! $elemento->dni !!}</strong>
                @endif
            </div>
            <div style="width: 100%; margin-bottom: 0px ;">
                <div style="float:left;width:69%;">
                    <p style="font-size: 6pt;  margin-top:40px;margin-bottom: 6px">{!! fullDireccion() !!}<br/>Telef: {!! config('contacto.telefono') !!}<br/>{!! config('contacto.web') !!}</p>
                    <div style="float:left;width:65%; text-align: center;margin-top: 10px;">
                        <strong style="font-size: 9pt">{!!$datosInforme[1]!!}</strong>
                    </div>
                    <div style="float:left;width:35%;text-align: center;">
                        <strong style="font-size: 7pt">Validesa<br/>
                            {!! $datosInforme[0] !!}-{!! $datosInforme[0]+3 !!}</strong>
                    </div>
                </div>
                <div style="float:left;width:29%;margin-right: 5px;">
                    @if ($elemento->foto)
                        <img style="border:black solid thin;float:right" src="{{public_path('/storage/'.$elemento->foto)}}" width="68px" height="90px" alt="Foto Carnet"/>
                    @else
                        <img style="border:black solid thin;float: right" src="" width="68px" height="90px" alt="Foto blanc" />
                    @endif
                </div>
            </div>

        </div>
    </div>
    @endforeach
@endsection