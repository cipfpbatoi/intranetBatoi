@extends('layouts.pdf')
@section('content')
    @php $empresa = $todos->Colaboracion->Centro; @endphp
    @foreach ($todos->Colaboradores as $instructor)

            <div class="page" style="font-size: large;line-height: 2em">
                @include('pdf.partials.cabecera')
                <br/>
                <div class="container col-lg-12" style="width:90%;">
                    <p style="text-indent: 50px">{{$datosInforme['consideracion']}}
                        <strong>{{$datosInforme['secretario']}}</strong>
                        @if ($datosInforme['consideracion'] == 'En')
                            secretari
                        @else
                            secretària
                        @endif del
                        {{$datosInforme['centro']}} d'{{$datosInforme['poblacion']}}, província
                        d'{{$datosInforme['provincia']}}.</p>
                </div>
                <div class="container">
                    <strong>CERTIFICA:</strong>
                    <br/>
                </div>
                <div class="container" style="width:95%">
                    <p style="text-indent: 30px;text-align: justify">Que l'empresa
                        <strong> {{$empresa->nombre}} </strong> , ubicada a {{$empresa->direccion}} de/d'
                        {{$empresa->localidad}}, ha col·laborat en les pràctiques corresponents a la Formació de Centres
                        de Treball (FCT) del {{$todos->Colaboracion->Ciclo->Xtipo}}
                        <strong>{{$todos->Colaboracion->Ciclo->vliteral}}</strong> realitzades durant el curs
                        lectiu {{curso()}}, i en les que han participat {{ count($todos->Alumnos) }} alumnes/as.
                    </p>
                    <p>Que dins d'aquesta empresa, En/Na/N' <strong>{{$instructor->name}} </strong> amb
                        DNI {{$instructor->idInstructor}}, ha col.laborat en l'instrucció dels alumnes en les pràctiques
                        formatives durant <strong>{{$instructor->horas}}</strong> hores.</p>
                </div>
                @include('pdf.partials.firmaSD')
            </div>

     @endforeach
@endsection
