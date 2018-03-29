@extends('layouts.pdf')
@section('content')
@include('pdf.partials.cabecera')
<div class="container col-lg-12" >
    <table class="table table-bordered">
        <tr>
            <th>Full d'absències del professorat</th>
        </tr>
    </table>
</div>
<div class="container col-lg-12" >
    <table class="table table-bordered" >
        <thead>
            <tr>
                <th>FUNCIONARI/A:</th><th>DNI</th><th>Motiu</th><th>Dates</th><th>Observacions</th><th>Document</th><th>Estat</th><th>Llarga Durada</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($todos as $elemento)
            <tr>
                <td>{!! $elemento->Profesor->apellido1 !!} {!! $elemento->Profesor->apellido2 !!} {!! $elemento->Profesor->nombre !!}</td>
                <td>{!! $elemento->idProfesor !!}</td>
                <td>{{Intranet\Entities\Falta::getMotivosOptions()[$elemento->motivos]}}</td>
                <td>{{$elemento->desde }}
                    @if ($elemento->desde == $elemento->hasta)
                        @if (!$elemento->dia_completo) 
                            {{ $elemento->hora_ini}}-{{$elemento->hora_fin}}
                        @endif
                    @else - {{$elemento->hasta }}
                    @endif
                </td>
                <td>{{$elemento->observaciones }}</td>
                <td>{{$elemento->id}}</td>
                <td>@if ($elemento->estado == 4) <span>-Resolta-</span>  @endif
                    @if ($elemento->estado == 3) <span>-Autorizada-</span>  @endif
                    @if ($elemento->estado < 3) <span>-No Autorizada-</span> @endif
                    
                    @if ($elemento->fichero) <span>Justificant</span> @endif
                </td>
                <td>
                    @if ($elemento->estado == 5) <span>X</span> @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
<br/><br/><br/><br/><br/>
<p>La direcció AUTORITZA les baixes durant el/s die/s i hor/es indicats</p>
<div class="container col-lg-12">
    <div style="width:50%;float:left">SIGNAT EL DIRECTOR:</div>
    <div style="width:50%;float:right;text-align: right">ALCOI A {{ $datosInforme }}</div>
</div>
@endsection




