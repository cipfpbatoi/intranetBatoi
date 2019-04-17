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
    <p>T'escric per recordar-te l'inici de les pràctiques de FCT. A continuació et passe relació dels alumnes que t'han estat assignat i les dades de començament de les pràctiques.</p>
    @foreach ($elemento->fcts as $fct)
        <p>Instructor: {{$fct->Instructor->Nombre}}</p>
        <p>Data de començament: {{$fct->desde}} </p>
        <p>Alumnes assignats: </p>
        <ul>
        @foreach ($fct->Alumnos as $alumno)
            <li> {{$alumno->fullName}} - {{$alumno->email}} </li>
        @endforeach
        </ul>
    @endforeach
    <p>Aprofite per donar-te les meues dades per si necessiteu possar-se amb contacte amb mi:<br/>
        Tutor: {{AuthUser()->fullName}} {{AuthUser()->email}} <br/>
        Telèfon centre: {{ config('contacto.telefono') }} <br/>
    </p>
    <p>Així com també informació relevant en cas <a href="http://www.ceice.gva.es/va/web/formacion-profesional/seguro">d'accident laboral</a></p>
    <p>Per qualsevol dubte em tens a la teua disposició</p>
    <p>Salutacions cordials de {{AuthUser()->shortName}}</p>

</div>
@endsection