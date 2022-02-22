@extends('layouts.intranet')
@section('content')
	<p>Propuesta de modificación del horario que deberá ser aprobada por jefatura de estudios.</p>
	<p>Sobre tu horario original cambia las horas arrástrandolas a donde desees y cuando lo tengas pulsa el botón de 'Guardar cambios'.</p>
	<p>Cuando lo haya aprobado jefatura te aparecerá tu horario cambiado.</p>
	<p>En el campo observaciones debes poner el nº de horas que liberas y a qué tareas dedicarás cada una de dichas horas.</p>
	<strong>Estado: </strong><input type="text" id="estado" readonly><br>
	<button id="init">Iniciar Procedimiento</button>
@include('home.partials.horario.profesor')
	Observaciones: <textarea class="form-control" id="obs" placeholder="Indica el nº de horas liberadas y a qué las dedicarás"></textarea><br>
	<button id="guardar">Guardar cambios y enviar</button>
        <button id="aplicar" hidden="on">Aplicar cambio horario</button>
@endsection
@section('titulo')
Horario {{$profesor->fullName}}
@endsection
@section('scripts')
{{ Html::script("/js/Horario/cambiar.js") }}
@endsection

