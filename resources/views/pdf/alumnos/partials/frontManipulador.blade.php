@include('pdf.partials.cabecera')
<div class="container col-lg-12" style="width:95%;clear:right;text-align: justify;">
    <br/><br/><br/>
    <strong>{{\Intranet\Entities\Profesor::find(config('contacto.director'))->FullName}}</strong>
    <br/><br/><br/>
    Con DNI número {{\Intranet\Entities\Profesor::find(config('contacto.director'))->dni}}, como Director del C.I.P de F.P. Batoi, y según el programa
    de formación de manipuladores de alimentos que se imparte a los alumnos de este centro,
</div>
<br/><br/>
<div class="container col-lg-12" style="width:40%;float: center">
    <h2><strong>CERTIFICA</strong></h2>
</div>
<br/><br/>
<div class="container" style="width:95%;clear:right;text-align: justify">
    <p>Que @if ($elemento->Alumno->sexo == 'H') Don @else Doña @endif {{$elemento->Alumno->FullName}} con DNI número {{$elemento->Alumno->dni}} ha recibido la formación general en prácticas higiénicas de manipulación de alimentos y específica en la actividad de</p>
</div>
<div class="container col-lg-12" style="width:50%;float: center">
    <h2><strong>COMIDAS PREPARADAS</strong></h2>
</div>
<br/><br/>
<div class="container" style="width:95%;clear:right;text-align: justify">
    <p>realizado en los dias de {{$datosInforme->fecha_inicio}} a {{$datosInforme->fecha_fin}} (expediente de curso 00{{$datosInforme->id}}/{{Curso()}}) con un total de {{$datosInforme->horas}} horas.</p>
</div>
<br/><br/>
<div class="container" style="width:95%;clear:right;text-align: justify">
    <p>El presente certificado se emite para que conste y sirva de justificante a los efectos de acreditación de aprovechamiento de los programas o actividades
        de formación de manipuladores de alimentos.</p>
</div>
<div class="container" style="width:50%;float: center">
    <br/><br/><br/><br/>
    
    <p>En {{config('contacto.poblacion')}}, a {{FechaString($datosInforme->fecha_fin)}} </p>
</div>