@extends('layouts.intranet')
@section('css')
<title></title>
@endsection
@section('content')
@include('grupotrabajo.partials.profesoresTabla')
<a href="/grupotrabajo" class="btn btn-success">@lang("messages.buttons.atras") </a>
@endsection
@section('scripts')
@endsection
