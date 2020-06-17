@php $elemento = $todos @endphp
@extends('layouts.pdf')
@section('content')
    <div class="page" style='font-size: 1.5em'>
        @include('pdf.alumnos.partials.frontManipulador')
    </div>
    <div class="page" style='font-size: 1.5em'>
        @include('pdf.alumnos.partials.backManipulador')
    </div>
@endsection
