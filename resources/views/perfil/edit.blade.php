@extends('intranet.edit')
@section('after')
{!! Field::checkboxes('rol',config('roles.lor'),rolesUser($formulario->getElemento()->rol),['inline','roles'=>['2','11']]) !!}
@endsection

