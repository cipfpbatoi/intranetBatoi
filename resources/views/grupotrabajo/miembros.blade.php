@extends('layouts.intranet')
@section('css')
<title></title>
@endsection
@section('content')
@include('grupotrabajo.partials.profesoresTabla')
<a href="{{ route('grupotrabajo.index') }}" class="btn btn-success">@lang("messages.buttons.atras") </a>
@endsection
@section('scripts')
@endsection
