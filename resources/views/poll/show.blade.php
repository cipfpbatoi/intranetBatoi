@extends('layouts.show')
@section('show_content')
@foreach ($polls as $poll)
{{ PollWriter::draw($poll->id, auth()->user()) }}
@endforeach
@endsection