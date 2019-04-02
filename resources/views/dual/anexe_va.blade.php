@extends('layouts.pdf')
@section('css')
    {{ Html::style('/css/dual.css') }}
@endsection
@section('content')
        @include('dual.partials.anexe_va',['imagen'=>'img/pdf/dual/anexe_va_i.jpg','top'=>0])
        @include('dual.partials.anexe_va',['imagen'=>'img/pdf/dual/anexe_va_ii.jpg','top'=>1000])
@endsection