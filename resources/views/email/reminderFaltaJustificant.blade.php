@extends('layouts.email')

@section('body')
    <div style="width: 800px; text-align: justify; font-size: larger;">
        <p>Hola {{ $falta->Profesor->fullName ?? $falta->idProfesor }},</p>

        <p>
            Et recordem que tens una falta d'assistència del dia
            <strong>{{ $falta->desde }}</strong>
            pendent de justificar.
        </p>

        <p>
            Per favor, adjunta el justificant corresponent en la intranet al més prompte possible.
        </p>

        <p>Gràcies.</p>
    </div>
@endsection
