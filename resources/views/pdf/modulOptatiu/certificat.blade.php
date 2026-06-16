@extends('layouts.pdf')

@section('content')
    @php
        $alumne = $todos['alumne'];
        $certificat = $todos['certificat'];
        $resultat = $todos['resultat'];
        $nota = config('auxiliares.notas')[(int) $resultat->nota] ?? $resultat->nota;
        $dni = preg_replace('/^0/', '', (string) $alumne->dni);
    @endphp
    <div class="page" style="padding-top: 36px;">
        @include('pdf.partials.cabecera')
        <h2 style="text-align:center; margin-top: 40px;">CERTIFICAT DEL MÒDUL OPTATIU</h2>

        <p style="font-size: 1.2em; line-height: 175%; text-align: justify; margin-top: 70px;">
            {{ $datosInforme['director']['articulo'] ?? 'La' }}
            {{ $datosInforme['director']['genero'] ?? 'directora' }}
            del {{ config('contacto.titulo') }}, certifica que
            {{ nomAmbTitol($alumne->sexo, $alumne->fullName) }}, amb DNI núm. {{ $dni }},
            ha cursat el mòdul optatiu <strong>{{ $certificat->denominacio }}</strong>
            amb una qualificació de <strong>{{ $nota }}</strong>.
        </p>

        <div style="margin-top: 230px;">
            @include('pdf.partials.firmaDS')
        </div>
    </div>
@endsection
