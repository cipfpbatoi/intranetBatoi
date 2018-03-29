@extends('intranet.edit')
@section('after')
{!! Field::checkboxes('rol',config('constants.lor'),rolesUser($elemento->rol),['inline','roles'=>['2','11']]) !!}
@endsection

