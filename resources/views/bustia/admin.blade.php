@extends('layouts.intranet')
 @section('css')
    <title>Administrador Busties</title>
    <style>
        table, th, td {
            border: 1px solid;
        }
    </style>
    @livewireStyles
@endsection
@section('content')
    @livewire('bustia-violeta.admin-list')
@endsection
@section('titulo')
Administrador Busties
@endsection
@section('scripts')
    @livewireScripts
    <script>
  // Bootstrap 4 + Livewire
  document.addEventListener('livewire:load', function () {
    window.addEventListener('open-contact', function () {
      $('#contactModal').modal('show');    // 👈 obri modal
    });
    window.addEventListener('close-contact', function () {
      $('#contactModal').modal('hide');    // 👈 tanca modal
    });
    window.addEventListener('open-message', function(){
      $('#messageModal').modal('show');
    });
    window.addEventListener('close-message', function(){
      $('#messageModal').modal('hide');
    });
  });
</script>
@endsection

