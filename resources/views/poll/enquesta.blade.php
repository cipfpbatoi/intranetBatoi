@extends('layouts.intranet')
@section('css')
<title>{{trans("models.Poll.show")}}</title>
@endsection
@section('content')
    <form method="post" action="/alumno/poll/{{$poll->id}}">
        @csrf
        <div id="wizard" class="form_wizard wizard_verticle">
            @include('poll.partials.wizard_head')
            @include('poll.partials.'.$poll->que)
        </div>  
    </form>
@endsection
@section('titulo')
{{$poll->title}}
@endsection
@section('scripts')
{{ Html::script('/js/Poll/create.js') }}
@endsection
