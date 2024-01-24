@extends('layouts.email')
@section('body')
    <div style="width: 800px; text-align: justify; font-size: larger">
        <p><strong>De {{authUser()->fullName}} del {{config('contacto.nombre')}} </strong></p>
        <p>Adjunte document <strong>{{$signatura->tipus}}</strong> de la Fct de <strong>{{ $signatura->Fct->Alumno->fullName }}</strong> per a la seua Signatura.
        Cal signar-lo i tornar-lo o pujar-lo en l'intranet.</p>
    </div>
@endsection
