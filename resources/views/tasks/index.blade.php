@extends('layouts.intranet')
@section('css')
    @livewireStyles
@endsection
@section('content')
    @livewire('datatables', ['mod' => 'Profesor'])
@endsection
@section('scripts')
    @livewireScripts
@endsection
