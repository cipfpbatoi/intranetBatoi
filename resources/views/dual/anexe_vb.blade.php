@extends('layouts.pdf')
@section('css')
    {{ Html::style('/css/dual.css') }}
@endsection
@section('content')
        @include('dual.partials.anexe_vb',['imagen'=>'img/pdf/dual/anexe_vb_i.jpg','top'=>0])
        @include('dual.partials.anexe_vb',['imagen'=>'img/pdf/dual/anexe_vb_ii.jpg','top'=>1150])
@endsection