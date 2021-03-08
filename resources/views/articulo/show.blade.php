@extends('layouts.intranet')
@section('content')
<div class="col-md-6 col-sm-6 col-xs-12">
    <div class="x_panel">
        <div class="x_content">
            <div class="">
                <h3>Article {{ $elemento->descripcion }}</h3>
            </div>
            <hr/>
            <div class="float:both">
                <img src="{{asset('/storage/'.$elemento->fichero)}}" height="600px" with="900px" alt="Foto de l'article"/>
            </div>
        </div>
    </div>
</div>
@endsection
@section('titulo')
{{trans("models.$modelo.show")}} {{$elemento->getKey()}}
@endsection
