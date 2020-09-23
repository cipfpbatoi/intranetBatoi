@extends('layouts.pdf')
@section('content')
    @foreach ($todos as $grupo)
        <div class="page container col-lg-12" >
            <div class="container">
                <table class="table table-bordered">
                    <tr>
                        <th>Fotos Grup:{{$datosInforme->nombre}}</th>
                        <th>Tutor: @if (isset($datosInforme->Tutor->nombre)) {{$datosInforme->Tutor->fullName}} @endif</th>
                        <th>SubGrupo: {{$grupo->first()->subGrupo??'Desconegut'}}</th>
                    </tr>
                </table>
            </div>
            <div class="container">
                @php  $posicion = '';  @endphp
                @foreach ($grupo as $elemento)
                    @if ($posicion != substr($elemento->posicion,0,1))
                        @php
                            $posicion = substr($elemento->posicion,0,1);
                            $params = ['style'=>'clear:both'];
                        @endphp
                    @else
                        @php
                          $params = [];
                        @endphp
                    @endif
                    @include('pdf.alumnos.partials.foto',$params)
                @endforeach
            </div>
        </div>
    @endforeach
@endsection

