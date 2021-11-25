@extends('layouts.intranet')
@section('css')
<title>@lang("models.Reserva.edit")</title>
@endsection
@section('content')
<div class="formularionormal borderedondo">
    <div class="contenedor centrado">
        <br><h4 class="centrado">RESERVAR RECURSO</h4><br>

        <select id="recurso">
                <option value="0">-- Selecciona --</option>
                @foreach ($espacios as $espacio)
                    <option value="{{$espacio->aula}}">{{$espacio->descripcion}}</option>
                @endforeach
        </select>

        <div id="gestion">
            <br><label for="dia"> Día: </label>
            <input id="dia" type="text" name="dia" class="noFlotar date" autofocus />
            <div id="tableContainer" class="calendario">
                <table class="table" id="horario">
                    @foreach ($horas as $hora)
                    <tr>
                        <th>{{$hora->turno}} {{$hora->hora_ini}}-{{$hora->hora_fin}}</th>
                        <td id="hora-{{$hora->codigo}}">Libre</td>
                    </tr>
                    @endforeach                    
                </table>
            </div>
            <label for="desde"> Desde hora:</label>
            <select id="desde" name="desde" class="noFlotar">
                <option value="0">-- Selecciona --</option>
                @foreach ($horas as $hora)
                <option value="{{$hora->codigo}}">{{$hora->turno}} {{$hora->hora_ini}}-{{$hora->hora_fin}}</option>
                @endforeach
            </select>
            <br><label for="hasta"> Hasta hora:</label>
            <select id="hasta" name="hasta" class="noFlotar">
                <option value="0">-- Selecciona --</option>
                @foreach ($horas as $hora)
                <option value="{{$hora->codigo}}">{{$hora->turno}} {{$hora->hora_ini}}-{{$hora->hora_fin}}</option>
                @endforeach
            </select>
            <br><label for="idProfesor"> Profesor que fa la reserva:</label>
            <select id="idProfesor" name="idProfesor" class="noFlotar">
                <option value="0">-- Selecciona --</option>
                @foreach ($profes as $profe)
                    <option value="{{$profe->dni}}" @if ($profe->dni == AuthUser()->dni) selected @endif >{{$profe->apellido1}} {{$profe->apellido2}}, {{$profe->nombre}}</option>
                @endforeach
            </select>
            <br><label for="dia"> Observaciones: </label>
            <input id="observaciones" type="text" name="observaciones" class="noFlotar" autofocus />
            <div id="periodica">
                <br><label for="dia_fin"> Todos los <span id="nom_dia_fin"></span> hasta el día: </label>
                <input id="dia_fin" type="text" name="dia_fin" class="noFlotar date" autofocus />
            </div>
            <div id="botones">
                <input id="reservar" class="btn btn-danger" type="button" value="Reservar">
                <input id="liberar" class="btn btn-success" type="button" value="Liberar">
            </div>
            <div class="errores"></div>
        </div>
    </div>
</div>
@endsection
@section('titulo')
@lang("models.Reserva.edit")
@endsection
@section('scripts')
{{ Html::script("/js/Reserva/edit.js") }}
{{ Html::script("/js/datepicker.js") }}
@endsection