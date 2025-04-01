@extends('layouts.intranet')
@section('css')
    <title>Panel de control</title>
@endsection
@section('content')
    <div class="col-md-11 col-sm-11 col-xs-12">
         <x-user-tabs :tabs="[
    ['title' => 'messages.generic.nextActivities', 'view' => 'home.partials.activities', 'data' => ['actividades' => $actividades]],
    ['title' => 'messages.generic.faltas', 'view' => 'home.partials.faltas', 'data' => ['faltas' => $faltas, 'hoyActividades' => $hoyActividades, 'comisiones'  => $comisiones]],
    ['title' => 'messages.generic.tasks', 'view' => 'home.partials.tasks', 'data' => ['tasks' => $tasks]],
    ['title' => 'messages.generic.calendari', 'view' => 'home.partials.calendari' ],
    ['title' => 'messages.generic.timeTable', 'view' => 'home.partials.horario.corto', 'data' => ['horario' => $horario]],
    ['title' => 'messages.generic.reuniones', 'view' => 'home.partials.reuniones', 'data' => ['reuniones' => $reuniones]],

]" />
    </div>
@endsection
@section('titulo')
    @lang("messages.menu.Usuario")
@endsection
@section('scripts')
@endsection
