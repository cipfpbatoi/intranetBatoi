@extends('layouts.email')
@section('body')
    <div style="width: 800px; text-align: justify; font-size: larger">
        <p><strong>De {{authUser()->fullName}} del {{config('contacto.nombre')}} </strong></p>
    </div>
    <div style="width: 800px; text-align: justify; font-size: larger">
        <p>Adjunte document <strong>{{$elemento->tipus}}</strong> de la Fct de
            <strong>{{ $elemento->Fct->Alumno->fullName }}</strong> per a la seua Signatura.
            Cal signar-lo i tornar-lo.
        </p>
        <p>Adjunto documento <strong>{{$elemento->tipus}}</strong> de la Fct de
            <strong>{{ $elemento->Fct->Alumno->fullName }}</strong> para su Firma.
        </p>
    </div>
@endsection
