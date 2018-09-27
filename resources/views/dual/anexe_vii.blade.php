@extends('layouts.dual')
@section('content')
<div class="page" style="position:absolute;left:50%;margin-left:-421px;top:0px;width:842px;height:595px;border-style:outset;overflow:hidden">
    <div style="position:absolute;left:0px;top:0px"><img src="{{url('img/pdf/background1.jpg')}}" width=842 height=595></div>
    @include('dual.partials.anexe_vii')
</div>
<div class="page" style="position:absolute;left:50%;margin-left:-421px;top:605px;width:842px;height:595px;border-style:outset;overflow:hidden">
    <div style="position:absolute;left:0px;top:0px"><img src="{{url('img/pdf/background2.jpg')}}" width=842 height=595></div>
    @include('dual.partials.anexe_vii')
</div>
@endsection