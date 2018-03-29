@extends('layouts.intranet')
@section('css')
<title>{{trans("models.Programacion.seguimiento")}}</title>
@endsection
@section('content')
@include('programacion.partials.formCreate')
@endsection
@section('titulo')
{{trans("models.Programacion.seguimiento")}} 
@endsection
@section('scripts')
@endsection

