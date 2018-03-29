@extends('layouts.pdf')
@section('content')
    @foreach ($todos as $elemento)
        <div class="page" style='font-size: 1.5em'>
            @include('pdf.alumnos.partials.frontManipulador')
        </div>
        <div class="page" style='font-size: 1.5em'>
            @include('pdf.alumnos.partials.backManipulador')
        </div>
    @endforeach
@endsection
