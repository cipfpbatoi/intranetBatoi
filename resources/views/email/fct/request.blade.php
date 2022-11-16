@extends('layouts.email')
@section('body')
    <table style='text-align: center'>
        <tr>
            <th>Detalls Documentació Pràctiques FCT a confirmar</th>
        </tr>
    </table>
    <div>
        <table style=" border:#000 solid 1;">
            <tr>
                <td><strong>De {{authUser()->shortName}} del {{config('contacto.nombre')}} </strong></td>
            </tr>
        </table>
    </div>
    <div class="container">
        @include('email.fct.requestU')
    </div>
@endsection