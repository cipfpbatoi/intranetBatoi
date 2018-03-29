@extends('layouts.show')
@section('show_content')
@if ($elemento->enlace != '#')
<li><p><a href="{{$elemento->enlace}}" >Anar</a></p></li>
@endif
        <li><p> {{ trans("validation.attributes.idProfesor") }} : {{ $elemento->emissor }}</p></li>
        <li><p> {{ trans("validation.attributes.motivo") }} : {{ $elemento->motiu }}</p></li>
        <li><p> {{ trans("validation.attributes.fecha") }} : {{ $elemento->data }}</p></li>
@endsection

