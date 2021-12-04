@extends('layouts.intranet')
@section('css')
<title>@lang("models.Reserva.edit")</title>
@endsection
@section('content')
<div class="container formularionormal borderedondo">
    <div class="contenedor centrado borderedondo" style="margin-bottom: 20px">
        <br />
        <h4 class="centrado">RESERVAR RECURSO
            <select id="recurso">
                <option value="0">-- Selecciona --</option>
                @foreach ($espacios as $espacio)
                    <option value="{{$espacio->aula}}">{{$espacio->descripcion}}</option>
                @endforeach
            </select>
        </h4>
    </div>
    <div  id="gestion" class=" container">
        <div class="container">
            <div class="row col-md-5" style="margin-top: 50px;">
                <div class="borderedondo" style="background-color: #d4d4d4;margin-left: 30px;padding: 10px">
                    <h5 class="centrado">-- TICKET ---</h5>
                    <br/>
                    <div class="form-group row">
                        <label for="desde" class="col-md-5"> Des d'hora:</label>
                        <div class="col-md-7">
                            <select id="desde" name="desde" class="noFlotar">
                                <option value="0">-- Selecciona --</option>
                                @foreach ($horas as $hora)
                                <option value="{{$hora->codigo}}">{{$hora->turno}} {{$hora->hora_ini}}-{{$hora->hora_fin}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="hasta" class="col-md-5"> Fins hora:</label>
                        <div class="col-md-7">
                            <select id="hasta" name="hasta" class="noFlotar">
                                <option value="0">-- Selecciona --</option>
                                @foreach ($horas as $hora)
                                <option value="{{$hora->codigo}}">{{$hora->turno}} {{$hora->hora_ini}}-{{$hora->hora_fin}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="idProfesor" class="col-md-5"> Profesor que fa la reserva:</label>
                        <div class="col-md-7">
                            <select id="idProfesor" name="idProfesor" class="noFlotar">
                                <option value="0">-- Selecciona --</option>
                                @foreach ($profes as $profe)
                                    <option value="{{$profe->dni}}" @if ($profe->dni == AuthUser()->dni) selected @endif >{{$profe->apellido1}}, {{$profe->nombre}}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="form-group row">
                        <label for="dia" class="col-md-5"> Observacions: </label>
                        <div class="col-md-7">
                            <input id="observaciones" type="text" name="observaciones" class="noFlotar" autofocus />
                        </div>
                    </div>
                    <div id="periodica" class="form-group row">
                        <label for="dia_fin" class="col-md-5"> Tots els <span id="nom_dia_fin"></span> fins el dia: </label>
                        <div class="col-md-7">
                            <input id="dia_fin" type="date" name="dia_fin"  autofocus />
                        </div>
                    </div>
                    <div id="botones">
                        <input id="reservar" class="btn btn-danger" type="button" value="Reservar">
                        <input id="liberar" class="btn btn-success" type="button" value="Alliberar">
                    </div>
                    <div id="errores" class="errores"></div>
                </div>

            </div>

            <div class="row col-md-6" style="margin-left: 30px" >
                <div id="tableContainer" class="calendario borderedondo" >
                    <div style="font-size: large;padding-left: 25px;background-color: #d4d4d4">
                        <label for="dia"><span id="nom_dia_semana"></span></label>
                        <a id="reward" class="btn btn-small" href="#"><i class="fa fa-angle-double-left"></i></a>
                        <a id="back" class="btn btn-small" href="#"><i class="fa fa-angle-left"></i></a>
                        <input id="dia" type="date" name="dia" value="{{Hoy()}}" min="{{Hoy()}}" autofocus />
                        <a id="next" class="btn btn-small" href="#"><i class="fa fa-angle-right"></i></a>
                        <a id="forward" class="btn btn-small" href="#"><i class="fa fa-angle-double-right"></i></a>
                    </div>
                    <table class="table" id="horario" >
                        @foreach ($horas as $hora)
                            <tr>
                                <th>{{$hora->turno}} {{$hora->hora_ini}}-{{$hora->hora_fin}}</th>
                                <td id="hora-{{$hora->codigo}}" class="hora" style="font-weight: bold">Lliure</td>
                            </tr>
                        @endforeach
                    </table>
                </div>
            </div>
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