@extends('layouts.intranet')
@section('css')
<title>Confirmació {{$model}}</title>
@endsection
@section('content')
    <x-modal name="dialogo" title='Enviar dades a direccio'
             message='Enviar {{$model}} a direcció' clase="{{$model}}">
    @method('get')
    <span id="campos">
        <ul>
        @foreach ($element->showConfirm() as $key => $value)
                <li>@lang('validation.attributes.'.$key) : {{ $value }}</li>
        @endforeach
        </ul>
    </span>
    </x-modal>
@endsection
@section('titulo')
    Confirmació {{$model}}
@endsection
@section('scripts')
    <script>
        $(function () {
            $("#dialogo").modal("show");
        });
        $('button.btn.btn-primary').on('click', function (event) {
            event.preventDefault();
            location.href='/{{strtolower($model)}}/{{$id}}/init/';
        });
        $('button.btn.btn-default').on('click', function (event) {
            location.href='/{{strtolower($model)}}/';
        });
    </script>
@endsection
