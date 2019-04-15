@extends('layouts.email')
@section('body')
    <table style='text-align: center'>
        <tr>
            <th>{{$mail->getToPeople()}}</th>
        </tr>
    </table>
    <div>
        <table style=" border:#000 solid 1;">
            <tr >
                <td><strong>De {{$mail->getFromPerson()}} del {{config('contacto.nombre')}} </strong></td>
            </tr>
        </table>
    </div>
    <div class="container" >
            <p>Hola, </p>
            {!! $mail->getContent() !!}
    </div>
@endsection