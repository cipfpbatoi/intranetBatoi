@php
    $elementos = $panel->getElementos($pestana);
    $grupoCodes = $elementos->pluck('grupo_codigo')->filter()->unique()->values();
    $grupos = $grupoCodes->isEmpty()
        ? collect()
        : \Intranet\Entities\Grupo::whereIn('codigo', $grupoCodes)->get()->keyBy('codigo');
@endphp

@if ($grupoCodes->isEmpty())
    <div class="col-xs-12">
        <div class="well">No hi ha dades per al resum.</div>
    </div>
@else
    @foreach ($grupoCodes as $codigo)
        @php($grupo = $grupos->get($codigo))
        @if ($grupo)
            <div class="col-md-6 col-sm-6 col-xs-12 profile_details" style='font-size: x-large'>
                <div id="{{ $grupo->codigo }}" class="well profile_view">
                    <div class="col-sm-12">
                        <h4 class="brief"><i>{{ $grupo->nombre }}</i></h4>
                        <div class="left col-xs-12">
                            <h2>{{ $grupo->Ciclo->literal }} </h2>
                        </div>
                        <div class="left col-xs-12">
                            <p><strong>Alumnes Matriculats: </strong> {{ $grupo->matriculados }} </p>
                        </div>
                        <div class="left col-xs-12">
                            <ul class="list-unstyled">
                                <li>Resultats Fct: <b>{{ $grupo->resfct }}</b></li>
                                <li>Exempts: <b>{{ $grupo->exentos }}</b></li>
                                @if ($grupo->proyecto)
                                    <li>Resultats Projecte: <b>{{ $grupo->respro }} </b></li>
                                @endif
                                <li>Inserció Laboral: <b>{{ $grupo->resempresa }}</b></li>
                                @if ($grupo->acta)
                                    <li>Acta <b>{{ $grupo->acta }}</b></li>
                                @endif
                                <li>Documentació Entrevistes <b>@if ($grupo->calidad == 'O') Entregada @else Pendent @endif</b></li>
                            </ul>
                        </div>
                    </div>

                    <div class="col-xs-12 bottom text-center">
                        <div class="col-xs-12 col-sm-12 emphasis">
                            Tutor: {{ $grupo->Tutor->FullName ?? 'Desconegut' }}
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endforeach
@endif

