@extends('layouts.intranet')
@section('content')
@include('home.partials.horario.profesor')
@endsection
@section('titulo')
Horario {{ $profesor->FullName}}
@endsection

