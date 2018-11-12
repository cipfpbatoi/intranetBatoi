@include('pdf.partials.cabecera')
@php $director = \Intranet\Entities\Profesor::find(config('contacto.director')); @endphp
<div class="container col-lg-12" style="width:95%;clear:right;text-align: justify;">
    <br/><br/><br/>
    <strong>{{ $director->FullName }}</strong>
    <br/><br/><br/>
    Amb DNI número {{$director->dni}}, com a
    @if ($director->sexo == 'H') Director @else Directora @endif 
    del {{config('contacto.titulo')}}, i segons el programa
    de formació de manipuladors d'aliments que s'imparteix als alumnes d'aquest centre,
</div>
<br/><br/>
<div class="container col-lg-12" style="width:40%;float: center">
    <h2><strong>CERTIFIQUE</strong></h2>
</div>
<br/><br/>
<div class="container" style="width:95%;clear:right;text-align: justify">
    <p>Que @if ($elemento->Alumno->sexo == 'H') en/n' @else na/n' @endif {{$elemento->Alumno->FullName}} amb DNI número {{$elemento->Alumno->dni}} ha rebut la 
	formació general en pràctiques higièniques de manipulació d'aliments i específica en l'activitat de</p>
</div>
<div class="container col-lg-12" style="width:50%;float: center">
    <h2><strong>MENJARS PREPARATS</strong></h2>
</div>
<br/><br/>
<div class="container" style="width:95%;clear:right;text-align: justify">
    <p>realitzada els dies de {{$datosInforme->fecha_inicio}} a {{$datosInforme->fecha_fin}} (expediente de curso 00{{$datosInforme->id}}/{{Curso()}}) amb un total de {{$datosInforme->horas}} hores.</p>
</div>
<br/><br/>
<div class="container" style="width:95%;clear:right;text-align: justify">
    <p>El present certificat s'emet per a que conste i servisca de justificant als efectes d'acreditació d'aprofitament dels programes o activitats
		de formació de manipuladors d'aliments.</p>
</div>
<div class="container" style="width:50%;float: center">
    <br/><br/><br/><br/>
    
    <p>A {{config('constants.contacto.poblacion')}}, a {{FechaString()}} </p>
</div>