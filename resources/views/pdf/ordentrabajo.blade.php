@extends('layouts.pdf')
@section('content')
@include('pdf.partials.cabecera')
<div class="container col-lg-12" >
    <table class="table table-bordered">
        <tr>
            <th>Ordre de treball {{ $datosInforme->Tipos->literal }}</th>
        </tr>
    </table>
</div>
<div class="container col-lg-12" >
    <div>
        {{ $datosInforme->descripcion }}<br/>
        <br/><br/>
    </div>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Descripció</th><th>Espai</th><th>Realitzada</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($todos as $elemento)
            <tr>
                <td>{{$elemento->descripcion}}</td>
                <td>{{$elemento->espacio }}</td>
                <td>       </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    <div style="text-align: right">
        <br/><br/><br/>
        Data : {{ FechaString() }}
    </div>
</div>
@endsection




