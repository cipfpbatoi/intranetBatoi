@extends('layouts.pdf')
@section('content')
@include('pdf.partials.cabecera')
<br/>
<table class="table table-bordered">
    <tr>
        <th>Citació reunió</th>
    </tr>
</table>

<div class="container col-lg-12" >
   <br/> 
   Benvolguts senyors:
</div>
<div class="container" >
    <br/>
    <p>Em dirigisc a vostés com a tutor/a del seu fill/filla amb la finalitat de convidar-los a la reunió que tindrà lloc el dia <strong>{{$datosInforme->dia}}</strong> a les <strong>{{$datosInforme->hora}}</strong>
        a  l/la <strong>{{$datosInforme->Espacio->descripcion}}</strong> del CIPFP Batoi amb el següent ordre del dia.<p>
    <ul style='list-style:none'>
        @foreach ($todos as $elemento)
            <li>{{$elemento->orden}}.{{$elemento->descripcion}}</li>
        @endforeach    
    </ul>       
</div>
<div class="container">
    <br />
    Agraïnt-los la seua assistència i col.laboració per endavant. Una Salutació
</div>
@include('pdf.reunion.partials.signatura')
@endsection
