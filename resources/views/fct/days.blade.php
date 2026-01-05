@php
    // Acceptem tant $alumno com $alumnoFct per compatibilitat amb diferents controladors
    $alumnoFct = $alumnoFct ?? null;
    $student = $alumno ?? optional($alumnoFct)->Alumno;
    $studentId = optional($student)->id;
    $alumnoFctId = optional($alumnoFct)->id;
    $title = $student ? 'Calendari FCT de ' . $student->fullName : 'Calendari FCT';
@endphp

@extends('layouts.intranet')

@section('css')
    <livewire:styles />
@endsection

@section('titulo')
    {{ $title }}
@endsection

@section('content')
    @livewire('fct-calendar', ['alumno' => $studentId, 'alumnoFct' => $alumnoFctId])
@endsection

@section('scripts')
    <livewire:scripts />
@endsection
