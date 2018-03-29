@extends('layouts.intranet')
@section('css')
{{Html::style('/assets/datetimepicker/css/bootstrap-datetimepicker.css') }}
{{Html::style('/assets/datetimepicker/css/bootstrap-datetimepicker.min.css') }}
<title>{{trans("models.falta.imprime")}}</title>
@endsection
@section('content')
<h3>Impressió de faltes</h3>
<h4>Selecciona dates</h4>
<form method="POST" action='/direccion/falta/pdf'>
    {{ csrf_field() }}
    <label>Desde data:</label><input type='text' class="date" id='desde' name='desde'/><br/>
    <label>Fins data:</label><input type='text' class="date" id='hasta' name='hasta'/><br/>
    <label>Llistat mensual:</label><input type='checkbox' id='mensual' name='mensual' />
    <input type='submit' value='Enviar'/>
</form>
@endsection
@section('titulo')
{{trans("models.falta.imprime")}}
@endsection
@section('scripts')
{{ Html::script('/assets/moment.js') }}
{{ Html::script('/assets/datetimepicker/js/bootstrap-datetimepicker.min.js') }}
{{ Html::script("/js/datepicker.js") }}
@endsection

