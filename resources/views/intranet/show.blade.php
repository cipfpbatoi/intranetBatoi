@extends('layouts.intranet')
@section('content')
    <div class="col-md-6 col-sm-6 col-xs-12">
        <div class="x_panel">
            <div class="x_content">
                <div class="">
                    <ul class="to_do">
                        @foreach($elemento->getVisible() as $campo)
                            <li><p> {{ trans("validation.attributes.$campo") }} : {{ $elemento->$campo }}</p></li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('titulo')
    {{trans("models.$modelo.show")}} {{$elemento->getKey()}}
@endsection
