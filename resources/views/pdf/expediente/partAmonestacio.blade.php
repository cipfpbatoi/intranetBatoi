@extends('layouts.pdf')
@section('content')
    @foreach ($todos as $elemento)
        <div class="page">
            @include('pdf.partials.cabecera')
            <br/><br/><br/>
            <div class="container" style="width:95%;clear:right;text-align: justify">
                <p><strong>PART D'AMONESTACIÓ</strong></p>
                <p style="text-indent: 30px">ALUMNE/A: <strong>{{$elemento->Alumno->FullName}}</strong></p>
                <p style="text-indent: 30px">CURS/GRUP: <strong>{{curso()}}
                        /{{$elemento->Alumno->Grupo->first()->nombre}}</strong>
                <p style="text-indent: 30px">DATA: <strong>{{$elemento->fecha}}</strong> HORA: </p>
                <p style="text-indent: 30px">LLOC: <strong></strong></p> <b/>
                <p style="text-indent: 30px">D'acord amb el RRI del CIPFP BATOI (aprovat pel Consell Social el dia 20
                    d'abril de 2013). Per la present queda Vè. AMONESTAT per FALTA comesa en aquest Institut i que va
                    ser la següent: </p>
                <p tyle="text-indent: 30px"><strong>{{$elemento->explicacion}}</strong></p>
            </div>
            <div class="container" style="width:90%;">
                <br/><br/>
                <br/><br/><br/>
                <p tyle="text-indent: 30px">I per deixar-ne constància on procedeixi, signe la present amonestació.</p>
                <p>{{config('contacto.poblacion')}},a {{$datosInforme}} </p>
                <br/><br/><br/>
                <div style="width:45%; float:left;color:grey ">
                    <p><strong>PROFESOR: </strong></p>
                    <br/><br/>
                </div>
                <div style="width:45%; float:left;color:grey ">
                    <p><strong>CAPORALIA D'ESTUDIS: </strong></p>
                    <br/><br/>
                </div>
                <br/>
            </div>
            <br/><br/>
            <div class="container" style="clear: both">
                <hr/>
                <p>NOTA:<br/>
                <ul>
                    <li>Especificar el tipus de falta comesa: simple, lleu, greu, molt greu.</li>
                    <li>Explicar la causa per la qual es comet la falta, segons el RRI.</li>
                    <li>No podran imposar-se sancions per faltes greus o molt greus, sense la prèvia instrucció d'un
                        expedient.
                    </li>
                </ul>
            </div>
        </div>
    @endforeach
@endsection
