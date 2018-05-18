<div class="page" style="font-size:large;line-height: 2em">
    @include('pdf.partials.cabecera')
    <br/>
    <div class="container col-lg-12" style="width:90%;">
        <p style="text-indent: 50px">{{$datosInforme['consideracion']}}
            <strong>{{$datosInforme['secretario']}}</strong> 
            @if ($datosInforme['consideracion'] == 'En') secretari @else secretària @endif del 
            {{$datosInforme['centro']}} d'{{$datosInforme['poblacion']}}, província d'{{$datosInforme['provincia']}}.</p>
    </div>
    <div class="container" >
        <br/>
        <strong>CERTIFICA:</strong>
        <br/><br />
    </div>
    <div class="container" style="width:95%">
        <p style="text-indent: 30px;text-align: justify">Que segons consta en el seu expedient, @if ($todos->Alumno->sexo === 'H') l'alumne @else l'alumna @endif <strong>{{$todos->Alumno->FullName}} </strong> 
            amb DNI núm. {{$todos->Alumno->dni}}, ha realitzat la Formació en Centres de Treball (FCT) del <strong>{{$todos->Colaboracion->Ciclo->Xtipo}}
                {{$todos->Colaboracion->Ciclo->vliteral}} </strong> en l'empresa {{$todos->Colaboracion->Centro->Empresa->nombre}}, ubicada a {{$todos->Colaboracion->Centro->direccion}} de/d'
            {{$todos->Colaboracion->Centro->localidad}}, amb una duració total de {{$todos->horas}} hores, fetes
            entre {{$todos->desde}} i {{$todos->hasta}} i ha obtingut una qualifació d'APTE.</p>
    </div>
    <hr/>
    <div class="container" style="width:95%">
        <p style="text-indent: 30px;text-align: justify">Que según consta en su expediente, @if ($todos->Alumno->sexo === 'H') el alumno @else la alumna @endif <strong>{{$todos->Alumno->FullName}} </strong> 
            con DNI núm. {{$todos->Alumno->dni}}, ha realizado la Formación en Centros de Trabajo (FCT) del <strong>{{$todos->Colaboracion->Ciclo->Ctipo}}
                {{$todos->Colaboracion->Ciclo->cliteral}} </strong> en la empresa {{$todos->Colaboracion->Centro->Empresa->nombre}}, ubicada en {{$todos->Colaboracion->Centro->direccion}} de
            {{$todos->Colaboracion->Centro->localidad}}, con una duración total de {{$todos->horas}} horas, desempeñadas 
            entre el {{$todos->desde}} i el {{$todos->hasta}}, obteniendo una calificación de APTO.</p>
    </div>
    <div class="container" style="width:90%;">
        <br/><br/>
        <p><strong>I per tal que així conste on convinga, signa el present escrit.</strong></p>
        
        <p>{{$datosInforme['poblacion']}},a {{$datosInforme['date']}} </p>
        <br/><br/><br/><br/>
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

