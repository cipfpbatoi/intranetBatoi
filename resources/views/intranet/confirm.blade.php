@extends('layouts.intranet')
@section('css')
<title>{{$message}}</title>
@endsection
@section('content')
    <x-modal name="dialogo" title='{{$message}}'
             message='SI'  cancel="NO" dismiss='0' >
        @include('intranet.partials.components.showFields',['fields' => $element->showConfirm()])
    </x-modal>
@endsection
@section('titulo')
    {{$message}}
@endsection
@section('scripts')
    <script>
        $(function () {
            $("#dialogo").modal("show");
        });
        $('button.btn.btn-primary').on('click', function (event) {
            event.preventDefault();
            location.href= '{{ $route }}';
        });
        $('button.btn.btn-default').on('click', function (event) {
            event.preventDefault();
            location.href= '{{ $back }}';
        });
    </script>
@endsection
