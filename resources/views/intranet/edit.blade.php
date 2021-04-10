@extends('layouts.intranet')
@section('css')
<title>{{trans("models.$modelo.edit")}}</title>
@endsection
@section('content')
    {{ $formulario->render('put') }}
@endsection
@section('titulo')
{{trans("models.$modelo.edit")}} 
@endsection
@section('scripts')
@if (file_exists("js/$modelo/edit.js"))
    {{ Html::script("/js/$modelo/edit.js") }}
@endif
{{ Html::script("/js/datepicker.js") }}
@endsection

