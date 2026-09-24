@extends('layouts.email')

@section('body')
    <p>Bon dia, {{ $tutorName }},</p>
    <p>
        L’empresa <strong>{{ $confirmation->empresa->nombre }}</strong> ha revisat i confirmat les seues dades
        el {{ $confirmation->confirmed_at->format('d/m/Y') }} a les {{ $confirmation->confirmed_at->format('H:i') }}.
    </p>
    <p>Les dades actualitzades ja estan disponibles en la intranet.</p>
    <p>Salutacions,<br>CIPFP Batoi</p>
@endsection
