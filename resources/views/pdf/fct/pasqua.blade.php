@extends('layouts.pdf')
@section('content')
        <div style="font-size:large;line-height: 2em">
            @include('pdf.partials.cabecera')
            <br/>
            <div class="container col-lg-12" style="width:90%;">
                @php  $grupo = \Intranet\Entities\Grupo::find(AuthUser()->grupoTutoria); @endphp
                <p style="text-indent: 50px">D.Dña: {{ AuthUser()->fullName }} tutor de FCT del grupo:<strong> {{ $grupo->nombre  }}</strong>
                    del ciclo formativo: <strong>{{ $grupo->Ciclo->cliteral }}</strong> de la familia profesional:
                    {{  substr(AuthUser()->Ldepartamento,12) }}.
            </div>
            <div class="container" >
                <strong>COMUNICA:</strong>
                <br/>
            </div>
            <div class="container" style="width:95%">
                <p style="text-indent: 30px;text-align: justify">
                    Según Orden 77/2010 en su artículo 14.3, la relación del alumnado que realizará las prácticas formativas
                    durante el periodo escolar no lectivo de Pascua.
                </p>
                <ul>
                @foreach ($todos as $alumno)
                    @if ($alumno->festiusEscolars == 0)
                        <li>{{$alumno->fullName}}</li>
                    @endif
                @endforeach
                </ul>
            </div>
            <hr/>
            <div class="container" style="width:95%">
                <p style="text-indent: 30px;text-align: justify">Durante estas semanas sus centro de Prácticas permanecerán abiertos prestando el mismo
                    servicio por lo que pueden acudir en su horario habitual, y cumplir con su programa formativo,
                    con el fin de poder completar la realización de las prácticas en el periodo ordinario.</p>
                <p style="text-indent: 30px;text-align: justify">Durante estas semanas los/as alumnos/as y el centro podrán contactar de forma telefónica o via e-mail con su tutor/a para resolver cualquier incidencia que pudiera surgir.</p>
                <p style="text-indent: 30px;text-align: justify">Para que conste a los efectos oportunos,</p>
            </div>
            <div class="container col-lg-6" style='padding-right: 200px;float:right' >
                <p>{!! config('contacto.poblacion') !!} a {{ FechaString() }}</p><br/><br/>
                <p>Firma / Signatura :</p>
            </div>
        </div>
@endsection
