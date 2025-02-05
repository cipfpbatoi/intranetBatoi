@extends('layouts.intranet')

@section('css')
    <title>Panel de control</title>
@endsection

@section('content')
    <x-user-profile :usuario="$usuario" />

    <div class="col-md-9 col-sm-9 col-xs-12">
        <x-user-tabs :tabs="[
            ['title' => 'messages.generic.nextActivities', 'view' => 'home.partials.activities', 'data' => ['actividades' => $actividades]],
            ['title' => 'messages.generic.timeTable', 'view' => 'home.partials.horario.grupo', 'data' => ['horario' => $horario]],
        ]" />
    </div>
@endsection

@section('titulo')
    @lang("messages.menu.Usuario")
@endsection

@section('scripts')
@endsection

