@if ($panel->getElementos($pestana)->first())
@php 
$grupo = $panel->getElementos($pestana)->first()->Alumno->Grupo->first();  @endphp
<div class="col-md-6 col-sm-6 col-xs-12 profile_details" style='font-size: x-large'>
    <div id="{{$grupo->codigo}}" class="well profile_view">
        <div class="col-sm-12">
            <h4 class="brief"><i>{{ $grupo->nombre}}</i></h4>
            <div class="left col-xs-12">
                <h2>{{ $grupo->Ciclo->literal }} </h2>
            </div>
            <div class="left col-xs-12">
                <p><strong>Alumnes Matriculats: </strong> {{$grupo->matriculados}} </p>
            </div>
            <div class="left col-xs-12">
                <ul class="list-unstyled">
                    <li>Resultats Fct: <b>{{$grupo->resfct}}</b></li>
                    <li>Resultats Projecte: <b>{{$grupo->respro}} </b></li>
                    <li>Inserció Laboral: <b>{{$grupo->resempresa}}</b></li>
                    @if ($grupo->acta) Acta <b>{{ $grupo->acta }}</b> </li> @endif
                    <li>Documentació Qualitat<b> @if ($grupo->calidad == 'O') Entregada @else Pendent @endif</b></li> 
                </ul>
            </div>
        </div>
        
        <div class="col-xs-12 bottom text-center">
            <div class="col-xs-12 col-sm-12 emphasis">
                Tutor: {{$grupo->Tutor->FullName}}
             </div>
        </div>
    </div>
</div>
@endif


