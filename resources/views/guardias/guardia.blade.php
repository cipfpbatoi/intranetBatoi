@extends('layouts.intranet')
@section('css')
<title>@lang("models.Guardia.edit")</title>
{{Html::style('/assets/datetimepicker/css/bootstrap-datetimepicker.css') }}
{{Html::style('/assets/datetimepicker/css/bootstrap-datetimepicker.min.css') }}
@endsection
@section('content')
<div class="form_box">

</div>
@if (\Intranet\Entities\Guardia::estoy())
    <div id="profesores" style="float:right">
        <strong>Profesors de guardia:</strong><br/>
        <ul>
            @foreach (\Intranet\Entities\Guardia::ahora() as $guardia)
                <li>{{$guardia->Profesor->fullName}}</li>
            @endforeach
        </ul>
    </div>
@endif
<div class="form_box">
    <form class="form-horizontal form-label-left">
        <div class='form-group item'>
            <label for="dia" class="control-label col-md-3 col-sm-3 col-xs-12">@lang("validation.attributes.dia"): </label>
            <div class='col-md-6 col-xs-12 col-sm-6'>
                <input type="text" class='form-control date col-md-7 col-xs-12' id="dia" name="dia">
            </div>
        </div>
        <div class='form-group item'>
            <label for="hora" class="control-label col-md-3 col-sm-3 col-xs-12">@lang("validation.attributes.hora"): </label>
            <div class='col-md-6 col-xs-12 col-sm-6'>
                <select id="hora" class='form-control col-md-7 col-xs-12 select'>
                    @foreach ($horas as $hora)
                    <option value="{{$hora->codigo}}">{{$hora->hora_ini}}-{{$hora->hora_fin}}</option>
                    @endforeach
                </select>
            </div>
        </div>
        <fieldset>
            <legend class="centrado">@lang("models.Guardia.create")</legend>
            <div class='form-group item'>
                <label for="hecha" class="disabled control-label col-md-3 col-sm-3 col-xs-12">@lang("validation.attributes.guardia"): </label>
                <div class='col-md-6 col-xs-12 col-sm-6'>
                    <input type="checkbox" id="hecha" name="hecha" disabled autofocus class="form-control col-md-7 col-xs-12">
                </div>
            </div>
            <div class='form-group item'>
                <label for="obs" class="disabled control-label col-md-3 col-sm-3 col-xs-12">@lang("validation.attributes.observaciones"):</label>
                <div class='col-md-6 col-xs-12 col-sm-6'>
                    <textarea class="form-control col-md-7 col-xs-12" id="obs" name="obs" disabled rows="4" cols="30" placeholder="Observaciones respecto a la guardia"></textarea>
                </div>
            </div>
            <div class='form-group item'>
                <label class="control-label col-md-3 col-sm-3 col-xs-12" for="obs_per">@lang("validation.attributes.comentario"):</label>
                <div class='col-md-6 col-xs-12 col-sm-6'>
                    <textarea class="form-control col-md-7 col-xs-12" id="obs_per" name="obs_per" rows="4" cols="30" placeholder="Observaciones respecto a ti en la guardia"></textarea>
                </div>
            </div>

                 <div class='form-group item'>
                     @if (\Intranet\Entities\Guardia::estoy())
                        <a href="/guardia/control" class="btn btn-dark">Control Personal</a>
                     @endif
                    <input id="submit" class="btn btn-success" type="submit" value="Guardar">
                 </div>

        </fieldset>
        <div class="errores"></div>
    </form>
</div>

@endsection
@section('titulo')
@lang("models.Guardia.edit")
@endsection
@section('scripts')
{{ Html::script('/assets/moment.js') }}
{{ Html::script('/assets/datetimepicker/js/bootstrap-datetimepicker.min.js') }}
{{ Html::script("/js/datepicker.js") }}
{{ Html::script("/js/Guardia/edit.js") }}
@endsection
