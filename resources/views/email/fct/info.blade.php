@extends('layouts.email')
@section('body')
<table style='text-align: center'>
    <tr>
        <th></th>
    </tr>
</table>
<div>
    <table style=" border:#000 solid 1;">
        <tr >
            <td><strong>De {{AuthUser()->shortName}} del {{config('contacto.nombre')}} </strong></td>
        </tr>
    </table>
</div>
<div class="container" >
    @include('email.fct.infoU')
</div>
@endsection