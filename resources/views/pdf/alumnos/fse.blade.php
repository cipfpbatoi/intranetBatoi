@extends('layouts.pdf')
@section('content')
@include('pdf.partials.cabecera')
<div class="container col-lg-12" >
    <p><strong>COMUNICACIÓ A l'ALUMNAT SOBRE EL COFINANÇAMENT PEL FONS SOCIAL
        EUROPEU DE LA FORMACIÓ PROFESSIONAL GM I GS I BÀSICA 2ª OPORTUNITAT
        EN LA COMUNITAT VALENCIANA</strong></p>
    <table class="table table-bordered">
        <tr><th colspan="3">Centre Educatiu</th></tr>
        <tr><th>Codi</th><th>Denominació</th><th>Municipi</th></tr>
        <tr><td><span style="font-size: 12px">{{ config('contacto.codi') }}</span></td><td><span style="font-size: 12px">{{ config('contacto.nombre') }}</span></td><td><span style="font-size: 12px">{{ config('contacto.poblacion') }}</span></td></tr>
    </table>   
    <table class="table table-bordered" >
        <tr><td style="text-align: left"><strong>Nom del cicle: </strong><span style="font-size: 12px">{{$datosInforme->Ciclo->ciclo}}</span></td><td style="text-align: left; font-weight: bold">Grau:</td></tr>
        <tr><td style="text-align: left"><strong>Curs/Grup: </strong><span style="font-size: 12px">{{$datosInforme->nombre}}</span></td><td style="text-align: left; font-weight: bold">Data:</td></tr>
        <tr><td style="text-align: left"><strong>Tutor/a: </strong><span style="font-size: 12px">{{$datosInforme->Tutor->FullName}}</span></td><td style="text-align: left; font-weight: bold">Signatura:<br/><br/><br/><br/></td></tr>
    </table>   
    <p>S'ha facilitat la informació sobre el cofinançament del Fons Social Europeu dels cicles de FP GM i GS 
        i FP Bàsica de 2ª oportunitat a la Comunitat Valenciana a l'alumnat següent: </p>
    <table class="table table-bordered">
        <tr><th>Cognoms i Nom</th><th>Cognoms i Nom</th></tr><tr>
        @foreach ($todos as $key => $elemento)
        <td><span style="font-size: 12px">{{$elemento->apellido1.' '.$elemento->apellido2.', '.$elemento->nombre}}</span></td>
            @if ($key % 2 == 1) <tr/><tr> @endif  
        @endforeach
        </tr>
    </table>
</div>
@endsection

