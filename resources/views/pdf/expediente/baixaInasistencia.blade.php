@extends('layouts.pdf')

@section('content')
    @foreach ($todos as $elemento)
        @if ($grupoAlumno = \Intranet\Entities\AlumnoGrupo::where('idAlumno',$elemento->idAlumno)->first())
            @php
                $tutor = $elemento->Profesor ?? null;
                $fechaNoti = $elemento->fechatramite ? \Carbon\Carbon::parse($elemento->fechatramite)->format('d/m/Y') : null;
            @endphp

            <div class="page">
                @include('pdf.partials.cabecera')
                <br/><br/><br/>

                {{-- Bloc dades alumne --}}
                <div class="container col-lg-12" style="width:40%;float:right">
                    {{ $elemento->Alumno->FullName }}<br/>
                    {{ $elemento->Alumno->domicilio }}<br/>
                    {{ $elemento->Alumno->codigo_postal }} {{ $elemento->Alumno->Poblacion }}<br/>
                    {{ $elemento->Alumno->Provincia->nombre ?? 'Alacant' }}<br/>
                </div>

                {{-- Bloc tutor i data de notificació Ítaca --}}
                <div class="container" style="width:40%;float:left;font-size:12px;line-height:1.4;">
                    <strong>Tutor/a:</strong> {{ $tutor->FullName ?? '—' }}<br/>
                    <strong>Data notificació Ítaca:</strong> {{ $fechaNoti ?? '—' }}
                </div>

                <div class="container" style="width:95%;clear:both;text-align:justify">
                    <br/><br/><br/>
                    <p style="text-indent:30px">
                        Per la present li comunique que, donat el nombre de faltes d'assistència acumulades des de l'inici del curs,
                        es procedirà a l’anul·lació de la seua matrícula de {{ curso() }}
                        del {{ $grupoAlumno->Grupo->Ciclo->ciclo }},
                        seguint les instruccions de la Resolució d’inici de curs de la Secretaria Autonòmica d’Educació i
                        Investigació, per la qual es dicten les instruccions sobre ordenació acadèmica i d’organització de
                        l’activitat docent dels centres de la Comunitat Valenciana que durant el curs {{ curso() }}
                        impartisquen cicles formatius de Formació Professional.
                    </p>

                    {{-- Constància del criteri d’avís i de la notificació --}}
                    <p style="text-indent:30px">
                        D’acord amb la normativa, l’avís s’emet quan han transcorregut <strong>8 dies</strong> sense assistir a classe
                        o quan s’ha assolit el <strong>5&nbsp;%</strong> del total d’hores d’inassistència. Es deixa constància que el/la tutor/a
                        <strong>{{ $tutor->FullName ?? '—' }}</strong> va efectuar la notificació a Ítaca el dia
                        <strong>{{ $fechaNoti ?? '—' }}</strong>.
                    </p>

                    <p style="text-indent:30px">
                        L’interessat/da disposa de <strong>dos dies hàbils</strong>, des de la recepció d’aquest document,
                        per a presentar la documentació que justifique degudament la causa de les absències.
                    </p>

                    <p style="text-indent:30px">
                        Davant aquesta resolució, l’interessat/da pot presentar recurs d’alçada en el termini d’un mes
                        des de la seua notificació davant la Direcció Territorial d’Educació d’Alacant.
                    </p>
                </div>

                <div class="container" style="width:90%;">
                    <br/><br/><br/><br/>
                    <p>{{ config('contacto.poblacion') }}, a {{ $datosInforme }}</p>
                    <br/><br/><br/><br/><br/>

                    <div style="width:45%; float:left;">
                        <p><strong>{{ \Intranet\Entities\Profesor::find(config('avisos.director'))->FullName }}</strong></p>
                        <br/><br/><br/>
                        <p>{{ signatura('expediente') }}</p>
                    </div>

                    <div style="width:45%; float:right; text-align:left;">
                        <p><strong>{{ $tutor->FullName ?? '' }}</strong><br/>
                        Tutor/a del grup {{ $grupoAlumno->Grupo->codigo ?? $grupoAlumno->Grupo->nombre }}</p>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
@endsection