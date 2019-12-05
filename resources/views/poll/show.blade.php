@extends('layouts.intranet')
@section('css')
    <title>Resultat Enquesta {{$poll->title}}</title>
@endsection
@section('content')
    <!-- page content -->
    <div class="x_content">
        @include('poll.partials.resolts.'.$poll->que)
    </div>
    <!-- /page content -->
@endsection
@section('titulo')
   Resultat enquesta {{$poll->title}}
@endsection
@section('scripts')

@endsection

