@extends('layouts.intranet')
 @section('css')
    <title>Bustia</title>
    <style>
        table, th, td {
            border: 1px solid;
        }
    </style>
    @livewireStyles
@endsection
@section('content')
    @livewire('bustia-violeta.form')
@endsection
@section('titulo')
Bustia
@endsection
@section('scripts')
    @livewireScripts
    <script>
        window.addEventListener('confirm-submit', function (e) {
            var f = (e.detail && e.detail.finalitat) || 'escoltar';
            var anon = !!(e.detail && e.detail.anonimo);
            var extra = (f === 'parlar')
                ? "Has triat 'parlar': l'enviament no pot ser anònim."
                : (anon ? "Enviaràs com a anònim." : "Enviaràs amb les teues dades.");
            var msg = "Vols enviar la bústia ara?\n\n" + extra;

            if (confirm(msg)) Livewire.emit('doSubmit');
        });

    </script>
@endsection

