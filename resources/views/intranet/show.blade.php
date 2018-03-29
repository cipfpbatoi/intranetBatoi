@extends('layouts.show')
@section('show_content')
    @foreach($elemento->getVisible() as $campo)
        <li><p> {{ trans("validation.attributes.$campo") }} : {{ $elemento->$campo }}</p></li>
    @endforeach
@endsection

