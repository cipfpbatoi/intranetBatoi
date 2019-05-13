@extends('layouts.email')
@section('body')
    <table style='text-align: center'>
        <tr>
            <th></th>
        </tr>
    </table>
    <div>
        <table style=" border:#000 solid 1;">
            <tr >
                <td><strong>De {{AuthUser()->shortName}} del {{config('contacto.nombre')}} </strong></td>
            </tr>
        </table>
    </div>
    <div class="container" >
        <p>Hola {{$elemento->contacto}},</p>
        <p>T'escric per conèixer de primera ma com van les pràctiques FCT dels alumnes:
            <ul>
                @foreach ($elemento->Alumnos as $alumno)
                    <li> {{$alumno->fullName}} </li>
                @endforeach
            </ul>
        <p>Si tot està correcte et tornaria a contactar en aproximadament 15 dies per a fer una visita al centre de treball.</p>
        <p>Aprofite per recordar-te les meues dades per si necessites possar-te amb contacte amb mi:<br/>
            Tutor: {{AuthUser()->fullName}} {{AuthUser()->email}} <br/>
            Telèfon centre: {{ config('contacto.telefono') }} <br/>
        </p>
        <p>Per qualsevol dubte em tens a la teua disposició</p>
        <p>Salutacions cordials de {{AuthUser()->shortName}}</p>
    </div>
@endsection