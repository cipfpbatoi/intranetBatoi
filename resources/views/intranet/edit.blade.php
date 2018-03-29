@extends('layouts.intranet')
@section('css')
<title>{{trans("models.$modelo.edit")}}</title>
@endsection
@section('content')
@include('intranet.partials.formCreate',['method'=>'PUT'])
@endsection
@section('titulo')
{{trans("models.$modelo.edit")}} 
@endsection
@section('scripts')
@if (file_exists("js/$modelo/edit.js"))
    {{ Html::script("/js/$modelo/edit.js") }}
@endif
@if ($elemento->isDatepicker())
{{ Html::script("/js/datepicker.js") }}
@endif
@endsection

