@extends('layouts.show')
@section('show_content')
    @foreach($elemento->toArray()  as $key => $campo)
        <li><p> {{ trans("validation.attributes.$key") }} : {{ $campo }}</p></li>
    @endforeach
    <li><p>Roles : <span>{{ implode(',',NameRolesUser($elemento->rol)) }}</span></p></li>
@endsection

