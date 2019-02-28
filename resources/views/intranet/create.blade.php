@extends('layouts.intranet')
@section('css')
<title>{{trans("models.$modelo.create")}}</title>
@endsection
@section('content')
@include('intranet.partials.formCreate',['method'=>'POST'])
@endsection
@section('titulo')
{{trans("models.$modelo.create")}}
@endsection
@section('scripts')
@if (file_exists("js/$modelo/create.js"))
{{ Html::script("/js/$modelo/create.js") }}
@endif
@if ($elemento->existsDatepicker())
{{ Html::script("/js/datepicker.js") }}
@endif
@endsection
