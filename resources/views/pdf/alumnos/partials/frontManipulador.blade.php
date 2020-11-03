@include('pdf.partials.cabecera')
@php $director = cargo('director');  @endphp
<div class="container col-lg-12" style="width:95%;clear:right;text-align: justify;">
    <br/><br/><br/>
    <strong>{{ $director->FullName }}</strong>
    <br/><br/><br/>
    Amb DNI número {{$director->dni}}, com a
    @if ($director->sexo == 'H') Director @else Directora @endif 
    del {{config('contacto.titulo')}}, i segons el programa
    de formació de manipuladors d'aliments que s'imparteix a l'alumnat d'aquest centre,
</div>
<br/><br/>
<div class="container col-lg-12" style="width:40%;float: center">
    <h2><strong>CERTIFIQUE</strong></h2>
</div>
<br/><br/>
<div class="container" style="width:95%;clear:right;text-align: justify">
    <p>Que @if ($elemento->sexo == 'H') en/n' @else na/n' @endif {{$elemento->FullName}} amb DNI número {{$elemento->dni}} ha rebut la
	formació general en pràctiques higièniques de manipulació d'aliments i específica en l'activitat de</p>
</div>
<div class="container col-lg-12" style="width:50%;float: center">
    <h2><strong>MENJARS PREPARATS</strong></h2>
</div>
<br/><br/>
<div class="container" style="width:95%;clear:right;text-align: justify">
    <p>realitzada els dies de {{$datosInforme->fecha_inicio}} a {{$datosInforme->fecha_fin}} (expedient curs 00{{$datosInforme->id}}/{{Curso()}}) amb un total de {{$datosInforme->horas}} hores.</p>
</div>
<br/><br/>
@include('pdf.partials.firmaGen',
['title'=>"El present certificat s'emet per a que conste i servisca de justificant als efectes d'acreditació d'aprofitament dels programes o activitats
		de formació de manipuladors d'aliments.",'signatura'=>'certificado','fecha'=>FechaString()])
