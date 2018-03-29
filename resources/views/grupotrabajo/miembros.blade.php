@extends('layouts.intranet')
@section('css')
<title></title>
@endsection
@section('content')
@include('grupotrabajo.partials.profesoresTabla')
<a href="/grupotrabajo" class="btn btn-success">{{trans('messages.buttons.atras')}} </a>
@endsection
@section('scripts')
@endsection
