@extends('layouts.intranet')
@section('css')
{{Html::style('/assets/datetimepicker/css/bootstrap-datetimepicker.css') }}
{{Html::style('/assets/datetimepicker/css/bootstrap-datetimepicker.min.css') }}
<title>@lang("models.Falta.imprime")</title>
@endsection
@section('content')
<div class='x-content'>

    <div class='form_box'>
       <form method="POST" action='/direccion/falta/pdf' class='form-horizontal form-label-left'> 
    {{ csrf_field() }}
    <div class="form-group item">
        <label class="control-label col-md-3 col-sm-3 col-xs-12">Desde data:</label>
        <div class="col-md-6 col-sm-6 col-xs-12">
            <input type='text' class="date form-control" id='desde' name='desde'/>
        </div>
    </div>
    <div class="form-group item"><label class="control-label col-md-3 col-sm-3 col-xs-12">Fins data:</label><div class="col-md-6 col-sm-6 col-xs-12"><input type='text' class="date form-control" id='hasta' name='hasta'/></div></div>
    <div class="form-group item"><label class="control-label col-md-3 col-sm-3 col-xs-12">Llistat:</label>
    <div class="col-md-6 col-sm-6 col-xs-12"><select name='llistat' class="form-control">
        <option value='faltas'>Ausències</option>
        <option value='birret'>Oblit Birret</option>
        </select></div></div>
    <div class="form-group item"><label class="control-label col-md-3 col-sm-3 col-xs-12">Tancar mes i generar informe gestor Documental:</label><div class="col-md-6 col-sm-6 col-xs-12"><input type='checkbox' class="form-control" id='mensual' name='mensual' /></div></div>
    <input type='submit' class='btn btn-success'value='Enviar'/>
</form>
</div>
</div>
@endsection
@section('titulo')
@lang("models.Falta.imprime")
@endsection
@section('scripts')
{{ Html::script('/assets/moment.js') }}
{{ Html::script('/assets/datetimepicker/js/bootstrap-datetimepicker.min.js') }}
{{ Html::script("/js/datepicker.js") }}
@endsection

