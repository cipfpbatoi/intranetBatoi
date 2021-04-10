@extends('layouts.intranet')
@section('css')
<title>{{trans("models.$modelo.create")}}</title>
@endsection
@section('content')
 {{ $formulario->render('post') }}
@endsection
@section('titulo')
{{trans("models.$modelo.create")}}
@endsection
@section('scripts')
@if (file_exists("js/$modelo/create.js"))
{{ Html::script("/js/$modelo/create.js") }}
@endif
{{ Html::script("/js/datepicker.js") }}
@endsection
