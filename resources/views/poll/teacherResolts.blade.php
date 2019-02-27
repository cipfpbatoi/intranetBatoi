@php
    $byOptions = $myVotes->groupBy('option_id');
    $byAll = $byOptions->groupBy('idModuloGrupo');
@endphp
@endphp
@extends('layouts.intranet')
@section('css')
    <title>Resultat Enquesta {{$poll->title}}</title>
@endsection
@section('content')
    <!-- page content -->
    <div class="x_content">
        <table style="border: #00aeef 1px solid">
            <thead>
            <tr>
                <th>Mòdul</th>
                @foreach ($options_numeric as $item) <th>{{$item->question}} </th> @endforeach
            </tr>
            </thead>
                @foreach ($byAll as $moduloGrupo)
                    <tr>

                        <td>{{$moduloGrupo->first()->first()->ModuloGrupo->ModuloCiclo->Modulo->literal}}</td>
                        @foreach ($moduloGrupo as $option)
                            <td>
                                <span></span>{{$option->avg('value',2)}}
                            </td>
                        @endforeach
                    </tr>
                @endforeach
        </table>
    </div>
    <!-- /page content -->
@endsection
@section('titulo')
   Resultat enquesta {{$poll->title}}
@endsection
@section('scripts')

@endsection

