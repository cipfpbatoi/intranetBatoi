@extends('layouts.email')
@section('body')

    <div>
        <table style='text-align: center' style=" border:#000 solid 1;">
            <tr>
                <th>Comunicació Falta Professorat del teu Grup</th>
            </tr>
            <tr>
                <td><strong>De {{$remitente['nombre']}} </strong></td>
                <td>{{$remitente['email']}}</td>
            </tr>
        </table>
    </div>
    <div>
            <p>Hola, el professor {{$elemento->Nombre}} ha comunicat que faltarà de:</p>
            <p>
            @if ($elemento->dia_completo)
                {{$elemento->desde}} - {{$elemento->hasta}}
            @else
                {{$elemento->desde}} de {{$elemento->hora_ini}} - {{$elemento->hora_fin}}
            @endif
            </p>
            <p>El protocol ha seguir serà: </p>
            <ul>
                <li>si el profe falta a primera, dir al delegat i/o grup que vinguen més tard</li>
                <li>si és a última, no passa res perquè el grup s'anirà abans</li>
                <li>si està en mig, parlar amb el professor que fa classe després per si pot avançar</li>
                <li>en cas de no poder avançar, l'alumnat estarà a classe i es cuidaran els professors que estan al mateix corredor segons protocol de les guàrdies</li>
            </ul>
    </div>
@endsection