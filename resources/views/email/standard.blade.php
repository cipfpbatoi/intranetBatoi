@extends('layouts.email')
@section('body')

    <div>
        <table style='text-align: center' style=" border:#000 solid 1;">
            <tr>
                <th>{{$mail->getToPeople()}}</th>
            </tr>
            <tr>
                <td><strong>De {{$mail->getFromPerson()}} del {{config('contacto.nombre')}} </strong></td>
            </tr>
        </table>
    </div>
    <div>
            <p>Hola, </p>
            {!! $mail->getContent() !!}
    </div>
@endsection