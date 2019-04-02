@extends('layouts.pdf')
@section('css')
    {{ Html::style('/css/dual.css') }}
@endsection
@section('content')
        @include('dual.partials.anexe_vii',['imagen'=>'img/pdf/dual/anexe_vii_i.jpg','top'=>0])
        @include('dual.partials.anexe_vii',['imagen'=>'img/pdf/dual/anexe_vii_ii.jpg','top'=>910])
@endsection