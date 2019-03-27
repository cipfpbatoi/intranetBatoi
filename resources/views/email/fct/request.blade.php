@extends('layouts.email')
@section('body')
    <table style='text-align: center'>
        <tr>
            <th>Detalls Documentació Pràctiques FCT a confirmar</th>
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
            <p>Estic preparant la documentació corresponent a les pràctiques de FCT del cicle de {{$e}}lemento->Ciclo->cliteral}} , i necessitaria que em confirmàreu els següents detalls de la documentació oficial:<br/>
            <ul>
                <li>Empresa: {{$elemento->Centro->nombre }}</li>
                <li>CIF: {{$elemento->Centro->Empresa->nif}}</li>
                <li>Telèfon: {{$elemento->telefono}}</li>
                <li>Adreça: {{$elemento->Centro->direccion}}</li>
                <li>Poble: {{$elemento->Centro->localidad}}</li>
                <li>Email-Empresa : {{$elemento->centro->email}}</li>
                <li>Horari Pràctiques: {{$elemento->Centro->Horario}}</li>
            </ul>
            @if ({$elemento->concierto)
                <ul>
                    <li>Representant legal:  ____________________________________________________</li>
                    <li>DNI Representant legal: _________________________________________________</li>
                </ul>
            @endif
            @if ($instructor = $elemento->instructorPrincipal)
                <ul>
                    <li>Instructor: {{$instructor->fullName}}</li>
                    <li>E-mail instructor: {{$instructor->email}}</li>
                    <li>DNI   instructor: {{$instructor->dni}}</li>
                    <li>Telefono instructor: {{$instructor->telefono}}</li>
                </ul>
            @else
                <ul>
                    <li>Instructor: ___________________________________________</li>
                    <li>E-mail instructor: ____________________________________</li>
                    <li>DNI   instructor: _____________________________________</li>
                    <li>Telefono instructor: __________________________________</li>
                </ul>
            @endif

        <p>Salutacions cordials de {{AuthUser()->shortName}}</p>

    </div>
@endsection