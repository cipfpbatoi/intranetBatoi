@extends('layouts.pdf')
@section('content')
@for ($i=0;$i<8;$i++)
<div style='width:106.6mm;height:68.98mm;float:left;margin:0.28cm;border:1pt #0068BA solid '>
    <div style='width:95%;margin-top:4pt;border-left: 2px #0068BA solid' class="container col-lg-12 fondo" >
        <div style="float:left;width: 70%; margin-bottom: 0px;margin-left: 8pt; ">
            <br/>
            <p style="font-size: 12pt;text-align: left; margin-bottom: 0px;margin-top: 1px;"><strong >{!! $todos->FullName !!}</strong></p>
            <p style="font-size: 11pt;text-align: left; margin-bottom: 0px;margin-top: 1px;"> {!!$datosInforme!!}</p>
            <p style="font-size: 11pt;text-align: left; margin-bottom: 0px;margin-top: 1px;"> {!! $todos->email !!}</p>
            <br/>
        </div>
        <div style="float:right;width:20%;margin-top:5pt;margin-right:8pt;text-align: center">
            <div style="margin-bottom: 35pt"><img src="{{url('img/pdf/ue.png')}}" width="80px" height="80px"/></div>
        </div>
    </div>
    <div style='width:95%;border-left: 2px #0068BA solid' class="container col-lg-12" >
         <div style="float:left;width: 70%; margin-bottom: 0px;margin-left: 8pt; ">
            <p style="font-size: 10pt;text-align: left; margin-bottom: 0px;margin-top: 6px;"> {!! config('contacto.web') !!}</p>
            <p style="font-size: 10pt;text-align: left; margin-bottom: 0px;margin-top: 1px;"> Tel: {!! config('contacto.telefono') !!} Fax: {!! config('contacto.fax') !!}</p>
            <p style="font-size: 10pt;text-align: left; margin-bottom: 0px;margin-top: 6px;"> {!! config('contacto.direccion') !!}</p>
            <p style="font-size: 10pt;text-align: left; margin-bottom: 0px;margin-top: 1px;"> {!! config('contacto.poblacion') !!} - C.P.:  {!! config('contacto.postal') !!}</p>
            <p style="font-size: 10pt;text-align: left; margin-bottom: 0px;margin-top: 1px;"> {!! config('contacto.provincia') !!} - Espanya</p>
        </div>
        <div style="float:right;width:20%;margin-top:5pt;margin-right:8pt;text-align: center">
            <div style="margin-bottom: 8pt"><img src="{{url('img/pdf/conselleria.png')}} " width="80px" height="33px"/></div>
            <div style="margin-bottom: 8pt"><img src="{{url('img/pdf/cabecera2.jpg')}}" width="80px" height="60px"/></div>
        </div>
    </div>
</div>
@endfor
@endsection
