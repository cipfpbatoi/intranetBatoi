@extends('layouts.intranet')
@section('css')
    <style>
        table, th, td {
            border: 1px solid;
        }
    </style>
    <livewire:styles />
@endsection
@section('content')
    @livewire('bustia-violeta.admin-list')
@endsection
@section('titulo')
Administrador Busties
@endsection
@section('scripts')
    <livewire:scripts />
    <script>
    // Bootstrap 4 + Livewire v3
    document.addEventListener('livewire:init', function () {
        Livewire.on('open-contact', () => $('#contactModal').modal('show'));
        Livewire.on('close-contact', () => $('#contactModal').modal('hide'));
        Livewire.on('open-message', () => $('#messageModal').modal('show'));
        Livewire.on('close-message', () => $('#messageModal').modal('hide'));
    });
    </script>
@endsection
