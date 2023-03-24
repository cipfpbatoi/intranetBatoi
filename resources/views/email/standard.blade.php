@extends('layouts.email')
@section('body')
    <div>
        <table style='text-align: center' style=" border:#000 solid 1;">
            <tr>
                <th>{{$mail->toPeople}}</th>
            </tr>
            <tr>
                <td><strong>De {{$mail->fromPerson}} del {{config('contacto.nombre')}} </strong></td>
            </tr>
        </table>
    </div>
    <div>
            {!!  Illuminate\Support\Facades\Blade::render(replaceCachitos($mail->view), ['elemento' => $elemento])  !!}
    </div>
@endsection
