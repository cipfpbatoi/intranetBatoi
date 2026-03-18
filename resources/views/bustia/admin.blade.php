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
    document.addEventListener('livewire:init', function () {
        const showModal = (id) => {
            if (window.intranetUiHelpers && typeof window.intranetUiHelpers.showModal === 'function') {
                window.intranetUiHelpers.showModal(id);
                return;
            }

            const modal = document.getElementById(id);
            if (modal && window.bootstrap && window.bootstrap.Modal) {
                window.bootstrap.Modal.getOrCreateInstance(modal).show();
            }
        };

        const hideModal = (id) => {
            if (window.intranetUiHelpers && typeof window.intranetUiHelpers.hideModal === 'function') {
                window.intranetUiHelpers.hideModal(id);
                return;
            }

            const modal = document.getElementById(id);
            if (modal && window.bootstrap && window.bootstrap.Modal) {
                window.bootstrap.Modal.getOrCreateInstance(modal).hide();
            }
        };

        Livewire.on('open-contact', () => showModal('contactModal'));
        Livewire.on('close-contact', () => hideModal('contactModal'));
        Livewire.on('open-message', () => showModal('messageModal'));
        Livewire.on('close-message', () => hideModal('messageModal'));
    });
    </script>
@endsection
