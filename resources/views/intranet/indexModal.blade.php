@extends('layouts.intranet')
@section('css')
    <title>{{$panel->getTitulo()}}</title>
@endsection
@foreach ($panel->getPestanas() as $pestana)
    @section($pestana->getNombre())
        <div class="centrado">@include('intranet.partials.buttons',['tipo' => 'index'])</div><br/>
        @include($pestana->getVista(),$pestana->getFiltro())
    @endsection
@endforeach
@section('titulo')
    {{$panel->getTitulo()}}
@endsection
@section('scripts')
    @include('intranet.partials.modal.index')
    @include('includes.tablesjs')
    @if ($elemento->isDatepicker())
        {{ Html::script("/js/datepicker.js") }}
    @endif
    @if (file_exists('js/'.$panel->getModel().'/grid.js'))
        {{ HTML::script('/js/'.$panel->getModel().'/grid.js') }}
    @else
        {{ HTML::script('/js/grid.js') }}
    @endif
     {{ HTML::script('/js/delete.js') }}
      @if (file_exists('js/'.$panel->getModel().'/modal.js'))
        {{ HTML::script('/js/'.$panel->getModel().'/modal.js') }}
      @else
         @if (file_exists('js/'.$panel->getModel().'/create.js'))
             {{ HTML::script('/js/'.$panel->getModel().'/create.js') }}
         @endif
      @endif   
     {{ HTML::script('/js/indexModal.js') }}
     
@endsection

