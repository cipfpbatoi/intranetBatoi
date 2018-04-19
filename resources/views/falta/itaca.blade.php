@extends('layouts.intranet')
@section('css')
<title>{{trans("models.Falta_itaca.edit")}}</title>
@endsection
@section('content')
<div class="formularionormal borderedondo">
    <div class="contenedor centrado" id='app'>
        <br><h4 class="centrado">{{ trans('models.modelos.Falta_itaca')}}</h4><br>
        <birret-itaca-view></birret-itaca-view>
    </div>
</div>
@endsection
@section('titulo')
{{trans("models.Falta_itaca.edit")}}
@endsection
@section('scripts')
{{ Html::script("/js/components/app.js")}}
@endsection