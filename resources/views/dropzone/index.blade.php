@extends('layouts.intranet')
@section('content')
<h4 class="centrado">{{trans("models.".ucfirst($modelo).".titulo",['quien'=>$quien])}}</h4>
@include('dropzone.partials.value')
@endsection
@section('scripts')
    {{ Html::script('/js/dropzone/link.js') }}
@endsection
