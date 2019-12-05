@extends('layouts.intranet')
@section('css')
<title>{{trans("models.Poll.show")}}</title>
@endsection
@section('content')
    <form method="post" action="/poll/{{$poll->id}}/do">
        @csrf
        <div id="wizard" class="form_wizard wizard_verticle">
            @include('poll.partials.wizard_head')
            @include('poll.partials.models.'.$poll->vista)
        </div>  
    </form>
@endsection
@section('titulo')
{{$poll->title}}
@endsection
@section('scripts')
{{ Html::script('/js/Poll/create.js') }}
@endsection
