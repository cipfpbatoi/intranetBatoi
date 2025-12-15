@extends('layouts.intranet')
 @section('css')
    <title>Bustia</title>
    <style>
        table, th, td {
            border: 1px solid;
        }
    </style>
    <livewire:styles />
@endsection
@section('content')
    @livewire('bustia-violeta.form')
@endsection
@section('titulo')
Bustia
@endsection
@section('scripts')
    <livewire:scripts />
    <script>
        Livewire.on('confirm-submit', function (payload) {
            var f = (payload && payload.finalitat) || 'escoltar';
            var anon = !!(payload && payload.anonimo);
            var extra = (f === 'parlar')
                ? "Has triat 'parlar': l'enviament no pot ser anònim."
                : (anon ? "Enviaràs com a anònim." : "Enviaràs amb les teues dades.");
            var msg = "Vols enviar la bústia ara?\n\n" + extra;

            if (confirm(msg)) Livewire.emit('doSubmit');
        });
    </script>
@endsection
