@extends('layouts.intranet')
@section('content')
    <div class="col-md-6 col-sm-6 col-xs-12">
        <div class="x_panel">
            <div class="x_content">
                <div class="">
                    <ul class="to_do">
                        @foreach($elemento->toArray()  as $key => $campo)
                            <li><p> {{ trans("validation.attributes.$key") }} : {{ $campo }}</p></li>
                        @endforeach
                        <li><p>Roles : <span>{{ implode(',',nameRolesUser($elemento->rol)) }}</span></p></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('titulo')
    {{trans("models.$modelo.show")}} {{$elemento->getKey()}}
@endsection