@extends('layouts.pdf')
@section('content')
<div class="container col-lg-12" >
    <table class="table table-bordered">
        <tr>
            <th>Fotos Grup:{{$datosInforme->nombre}}</th>
            <th>Tutor: @if (isset($datosInforme->Tutor->nombre)) {{$datosInforme->Tutor->fullName}} @endif</th>
        </tr>
    </table>
</div>
<div class="container col-lg-12">
    @foreach ($todos as $elemento)
        @include('pdf.alumnos.partials.foto')
    @endforeach
</div>
@endsection

