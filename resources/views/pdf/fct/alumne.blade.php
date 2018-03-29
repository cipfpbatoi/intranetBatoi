<div class="page">
    @include('pdf.partials.cabecera')
    <br/>
    <div class="container col-lg-12" style="width:90%;">
        <p style="text-indent: 50px">{{$datosInforme['consideracion']}}
            <strong>{{$datosInforme['secretario']}}</strong> 
            @if ($datosInforme['consideracion'] == 'En') secretario @else secretaria @endif del 
            {{$datosInforme['centro']}} de/d' {{$datosInforme['poblacion']}}, província de/d' {{$datosInforme['provincia']}}.</p>
    </div>
    <div class="container" >
        <br/>
        <strong>CERTIFICA:</strong>
        <br/><br />
    </div>
    <div class="container" style="width:95%">
        <p style="text-indent: 30px">Que segons consta en el seu expedient, @if ($todos->Alumno->sexo === 'H') En @else Na @endif <strong>{{$todos->Alumno->FullName}} </strong> 
            amb DNI núm. {{$todos->Alumno->dni}}, va realitzar la Formació en Centres de Treball (FCT) del <strong>Cicle Formatiu de grau
                {{$todos->Colaboracion->Ciclo->ciclo}} </strong> en l'empresa, ubicada a {{$todos->Colaboracion->Centro->direccion}} de/d'
            {{$todos->Colaboracion->Centro->localidad}}, amb una duració total de {{$todos->horas}} hores, les quals les va fer
            entre {{$todos->desde}} i {{$todos->hasta}} i va obtenir una qualifació d'APTE.</p>
    </div>
    <div class="container" style="width:90%;">
        <br/><br/>
        <p><strong>I per tal que així conste on convinga, signa el present escrit.</strong></p>
        <br/><br/><br/>
        <p>{{$datosInforme['poblacion']}},a {{$datosInforme['date']}} </p>
        <br/><br/><br/><br/><br/>
        <div style="width:45%; float:left; ">
            <p><strong>{{$datosInforme['secretario']}}</strong></p>
            <p>@if ($datosInforme['consideracion'] == 'En') SECRETARI @else SECRETARIA @endif</p>
        </div>
        <div style="width:45%; float:left; ">
            <p><strong>{{$datosInforme['director']}}</strong></p>
            <p>Vist-i-plau DIRECTOR</p>
        </div>
    </div>
</div>

