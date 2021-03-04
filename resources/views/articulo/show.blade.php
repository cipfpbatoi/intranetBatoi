@extends('layouts.intranet')
@section('content')
<div class="col-md-6 col-sm-6 col-xs-12">
    <div class="x_panel">
        <div class="x_content">
            <div class="">
                Article {{ $elemento->descripcion }}
            </div>
            <div class="float:both">
                <img src='/storage/{{ $elemento->fichero }}' />
            </div>
        </div>
    </div>
</div>
@endsection
@section('titulo')
{{trans("models.$modelo.show")}} {{$elemento->getKey()}}
@endsection
