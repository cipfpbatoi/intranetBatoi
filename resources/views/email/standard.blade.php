@extends('layouts.email')
@section('body')
    <div style="width: 800px; text-align: justify; font-size: larger">
        <p><strong>De {{$mail->fromPerson}} del {{config('contacto.nombre')}} </strong></p>
        {!!  Illuminate\Support\Facades\Blade::render(replaceCachitos($mail->view), ['elemento' => $elemento])  !!}
    </div>
@endsection
