@extends('layouts.pdf')
@section('content')
    @include('pdf.partials.cabecera')
    <br/>
    <div class="container col-lg-12">
        <table class="table table-bordered" style="width:100%">
            <caption><h3>{{$datosInforme->Tipos()->vliteral}}</h3></caption>
            <tr>
                <th scope="col"><h2>Equip docent del grup "{{$datosInforme->Xgrupo}}" - Curs {{curso()}}</h2></th>
            </tr>
            <tr>
                <td><h2>
                        @foreach ($datosInforme->profesores as $key => $profesor)
                            @if ($profesor->pivot->asiste == 1)
                                {{$profesor->nombre}} {{$profesor->apellido1}} {{$profesor->apellido2}},
                            @endif
                        @endforeach
                    </h2>
                </td>
            </tr>
        </table>
    </div>
    <div class="container col-lg-12">
        <p>Reunits els professors membres de l'equip docent del grup i havent analitzat les propostes presentades pels
            alumnes següents i que realitzaran en aquest curs i periode.
        </p>
        <p>
            Queden aprovades i s'ha fet l'assignament de tutories individuals per a cadascuna d'elles, quedant:
        </p>
    </div>
    <div class="container">
        <ul style='list-style:none'>
            @foreach ($todos as $elemento)
                <li><strong>{!! $elemento->descripcion !!}</strong>:</li>
                <li class="ident">{!! $elemento->resumen !!}</li>
            @endforeach
        </ul>
    </div>
    <div class="container">
        <br/>
        <div style="width:60%;float:left">
            <strong>Signatura professors:</strong>
            <br/><br><br/>
            <ul style='list-style:none'>
                @foreach ($datosInforme->profesores as $profesor)
                    @if ($profesor->pivot->asiste == 1)
                        <li style="height: 50px">{{$profesor->nombre}} {{$profesor->apellido1}} {{$profesor->apellido2}}</li>
                    @endif
                @endforeach
            </ul>
        </div>
        <div style="width:50%;float:left">
            <p><strong>VIST I PLAU CAPORALIA D'ESTUDIS</strong></p>
            <br/><br/><br/>
            <p>{{strtoupper(config('contacto.poblacion'))}} A {{$datosInforme->hoy}}</p>
        </div>
        @include('pdf.reunion.partials.signatura')
    </div>
@endsection
