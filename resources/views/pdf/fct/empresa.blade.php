@foreach ($todos->Instructores as $instructor)
<div class="page" style="font-size: large;line-height: 2em">
    @include('pdf.partials.cabecera')
    <br/>
    <div class="container col-lg-12" style="width:90%;">
        <p style="text-indent: 50px">{{$datosInforme['consideracion']}}
            <strong>{{$datosInforme['secretario']}}</strong> 
            @if ($datosInforme['consideracion'] == 'En') secretario @else secretaria @endif del 
            {{$datosInforme['centro']}} de/d' {{$datosInforme['poblacion']}}, província de/d' {{$datosInforme['provincia']}}.</p>
    </div>
    <div class="container" >
        <strong>CERTIFICA:</strong>
        <br/><br />
    </div>
    <div class="container" style="width:95%">
        <p style="text-indent: 30px;text-align: justify">Que l'empresa <strong> {{$todos->Colaboracion->Centro->Empresa->nombre}} </strong> , ubicada a {{$todos->Colaboracion->Centro->direccion}} de/d'
            {{$todos->Colaboracion->Centro->localidad}}, ha col·laborat en les pràctiques corresponents a la Formació de Centres de Treball (FCT) de l'alumne <strong>{{$todos->Alumno->FullName}} </strong> 
            , del {{$todos->Colaboracion->Ciclo->Xtipo}} <strong> {{$todos->Colaboracion->Ciclo->vliteral}} </strong>.Que dins d'aquesta empresa, En/Na <strong>{{$instructor->nombre}}</strong>, ha sigut l'instructor/a de les pràctiques
            formatives de l'alumne/a citat/ada. Que la seua participació com a instructor ha cobert la quantitat de {{$instructor->pivot->horas}} hores de la FCT de l'alumne/a esmentat/ada,
            les quals s'han realitzat del  {{$todos->desde}} fins {{$todos->hasta}}.</p>
            </p>
    </div>
    <hr/>
    <div class="container" style="width:95%">
        <p style="text-indent: 30px;text-align: justify">Que la empresa <strong> {{$todos->Colaboracion->Centro->Empresa->nombre}} </strong> , ubicada en {{$todos->Colaboracion->Centro->direccion}} de
            {{$todos->Colaboracion->Centro->localidad}}, ha colaborado en las prácticas correspondientes a la Formación de Centros de Trabajo (FCT) del alumno/a <strong>{{$todos->Alumno->FullName}}</strong>, del {{$todos->Colaboracion->Ciclo->Ctipo}} <strong> {{$todos->Colaboracion->Ciclo->cliteral}}</strong>.
            Que dentro de esta empresa, Don/Doña <strong>{{$instructor->nombre}}</strong>, ha sido el instructor/a de las prácticas
            formativas del alumno/a citado/da. Que su participación como instructor ha cubierto la cantidad de {{$instructor->pivot->horas}} horas de la FCT,
            que se han realizado desde el  {{$todos->desde}} hasta el {{$todos->hasta}}.</p>
            </p>
    </div>
    <div class="container" style="width:90%;">
        <br/>
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
@endforeach
