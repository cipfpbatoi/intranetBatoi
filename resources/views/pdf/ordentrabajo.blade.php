@extends('layouts.pdf')
@section('content')
    @include('pdf.partials.cabecera')
    <div class="container col-lg-12">
        <table class="table table-bordered">
            <tr>
                <th>Ordre de treball {{ $datosInforme->Tipos->literal }}</th>
            </tr>
        </table>
    </div>
    <div class="container col-lg-12">
        <div>
            {{ $datosInforme->descripcion }}<br/>
            <br/><br/>
        </div>
        <table class="table table-bordered">
            <thead>
            <tr>
                <th>Descripció</th>
                <th>Espai</th>
                <th>Professor</th>
                <th>Realitzada</th>
            </tr>
            </thead>
            <tbody>
            @foreach ($todos as $elemento)
                <tr>
                    <td>{{$elemento->descripcion}}.
                        @if (!empty($elemento->Observaciones))
                            Observacions: {{$elemento->Observaciones}}
                        @endif
                    </td>
                    <td>{{$elemento->espacio }}</td>
                    <td>{{$elemento->Creador->ShortName}}</td>
                    <td>@if ($elemento->estado>2)
                            X
                        @endif </td>
                </tr>
            @endforeach
            </tbody>
        </table>
        <div style="text-align: right">
            <br/><br/><br/>
            Data : {{ fechaString() }}
        </div>
    </div>
@endsection




