@extends('layouts.pdf')
@section('content')
@if ($datosInforme->Ciclo->tipo != 3)
    @include('pdf.partials.cabecera')
@else
    @include('pdf.partials.cabeceraBasica')
@endif
<div class="container" >
    <p><strong>COMUNICACIÓ A l'ALUMNAT SOBRE EL COFINANÇAMENT PEL FONS SOCIAL
        EUROPEU DE LA FORMACIÓ PROFESSIONAL GM I GS I BÀSICA
        EN LA COMUNITAT VALENCIANA</strong></p>
    <table class="table table-bordered" style="width: 1000px">
        <colgroup>
            <col style="width:20%" />
            <col style="width:50%" />
            <col style="width:30%" />
        </colgroup>
        <tr><th colspan="3">Centre Educatiu</th></tr>
        <tr><th>Codi</th><th>Denominació</th><th>Municipi</th></tr>
        <tr><td><span style="font-size: 16px">{{ config('contacto.codi') }}</span></td><td><span style="font-size: 16px">{{ config('contacto.nombre') }}</span></td><td><span style="font-size: 16px">{{ config('contacto.poblacion') }}</span></td></tr>
    </table>
    <br/><br/><br/>
    <table class="table table-bordered" style="width: 1000px">
        <colgroup>
            <col style="width: 15%">
            <col style="width: 35%">
            <col style="width: 50%">
        </colgroup>
        <tr><td style="font-size:20px;text-align: left">Nom del cicle </td><td style="font-size:20px;text-align: left" ><strong>{{$datosInforme->Ciclo->ciclo}}</strong></td><td style="font-size:20px;text-align: left">Grau: <strong>{{$datosInforme->Ciclo->tipo==1?'Mitjà':($datosInforme->Ciclo->tipo==2?'Superior':'Bàsic')}}</strong> </td></tr>
        <tr><td style="font-size:20px;text-align: left">Curs/Grup</td><td  style="font-size:20px;text-align: left"><strong>{{$datosInforme->nombre}}</strong></td><td  style="font-size:20px;text-align: left">Data:</td></tr>
        <tr><td style="font-size:20px;text-align: left">Tutor/a</td><td  style="font-size:20px;text-align: left"><strong>{{$datosInforme->Tutor->FullName}}</strong></td><td  style="font-size:20px;text-align: left">Signatura:<br/><br/><br/><br/></td></tr>
    </table>   
    <p style="font-size: 20px">S'ha facilitat la informació sobre el cofinançament del Fons Social Europeu dels cicles de FP GM i GS
        i FP Bàsica de 2ª oportunitat a la Comunitat Valenciana a l'alumnat següent: </p>
    <table class="table table-bordered" style="width:1000px">
        <colgroup>
            <col style="width: 50%">
            <col style="width: 50%">
        </colgroup>
        <tr><th>Cognoms i Nom</th><th>Cognoms i Nom</th></tr><tr>
        @foreach ($todos as $key => $elemento)
        <td><span style="font-size: 18px">{{$elemento->apellido1.' '.$elemento->apellido2.', '.$elemento->nombre}}</span></td>
            @if ($key % 2 == 1) <tr/><tr> @endif  
        @endforeach
        </tr>
    </table>
</div>
@include('pdf.partials.pie',['document'=>'actaFSE'])
@endsection

