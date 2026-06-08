@extends('layouts.intranet')
@section('css')
<title>{{__("models.Poll.show")}}</title>
@endsection
@section('content')
    <form method="post" action="/poll/{{$poll->id}}/do">
        @csrf
        <div id="wizard" class="form_wizard wizard_verticle">
            @include('poll.partials.wizard_head')
            @include('poll.partials.models.'.$poll->vista)
        </div>
        <div id="poll-submit-fallback" class="text-end" style="display:none; margin-top: 1.5rem;">
            <button type="submit" class="btn btn-success">Enviar enquesta</button>
        </div>
    </form>
@endsection
@section('titulo')
{{$poll->title}}
@endsection
@section('scripts')
{{ Html::script('/js/Poll/create.js') }}
@endsection
