@extends('intranet.edit')
 @section('after')
    @if (authUser()->rol % 17 === 0)
        <input type="hidden" name="DA" value="0">
        {!! Field::checkbox ('DA',1, $formulario->getElemento()->DA  ) !!}
    @endif
   {!! Field::checkboxes('rol',config('roles.lor'),rolesUser($formulario->getElemento()->rol),['inline','roles'=>['2','11']]) !!}
@endsection

