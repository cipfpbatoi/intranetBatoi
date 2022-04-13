@extends('layouts.pdf')
@section('content')
@include('pdf.partials.cabecera')
@php $agrupados = $todos->groupBy('idProfesor') @endphp
@foreach ($agrupados as $grupo)
    <div class='page'>
        <div class="container col-lg-12" >
            <table class="table table-bordered">
                <tr>
                    <th>Document de Pagament de les Comissions de Serveis</th>
                </tr>
            </table>
        </div>
        <div class="container col-lg-12" >
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>FUNCIONARI/A:</th><th>DNI</th><th>Itinerari</th><th>Exida</th><th>Tornada</th><th>Mitja de transport</th><th>Marca Vehicle</th><th>Matrícula</th><th>Concepte</th><th>Fct</th><th>Kilometraje</th><th>Total</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($grupo as $elemento)
                        <tr>
                            <td>{!! $elemento->Profesor->apellido1 !!} {!! $elemento->Profesor->apellido2 !!} {!! $elemento->Profesor->nombre !!}</td>
                            <td>{!! $elemento->idProfesor !!}</td>
                            <td>{{$elemento->itinerario}}</td>
                            <td>{{$elemento->desde }}</td>
                            <td>{{$elemento->hasta }}</td>
                            <td>{{$elemento->medio }}</td>
                            <td>{{$elemento->marca}}</td>
                            <td>{{$elemento->matricula}}</td>
                            <td>{{$elemento->descripcion}}€</td>
                            <td>{{$elemento->fct}}</td>
                            <td>{{$elemento->kilometraje }}</td>
                            <td>{{$elemento->total }}€</td>
                        </tr>
                    @endforeach
                    <tr>
                        <td colspan="9"><strong>Total {!! $grupo->first()->Profesor->apellido1 !!} {!! $grupo->first()->Profesor->apellido2 !!} {!! $grupo->first()->Profesor->nombre !!}</strong></td>
                        <td><strong>{{$grupo->sum('kilometraje') }}</strong></td>
                        <td><strong>{{$grupo->sum('total')}}€</strong></td>
                    </tr>
                </tbody>
            </table>
        </div>
        <br/><br/><br/><br/><br/>
        <p>La direcció AUTORITZA el pagament dels serveis prestats</p>
        <div class="container col-lg-12">
            <div style="width:50%;float:left">SIGNAT @if (\Intranet\Entities\Profesor::find(config('avisos.secretario'))->sexo == 'H') EL SECRETARIO @else LA SECRETARIA @endif:</div>
            <div style="width:50%;float:right;text-align: right">{{strtoupper(config('contacto.poblacion'))}} A {{ $datosInforme }}</div>
        </div>
    </div>
@endforeach
@endsection






