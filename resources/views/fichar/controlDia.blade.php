@extends('layouts.intranet')
@section('css')
<title>{{trans("models.Fichar.control")}}</title>
@endsection
@section('content')
    <div id="app">
    <div>
        <a class="pull-left prev-week">Semana anterior</a>
        <a class="pull-right next-week">Semana siguiente</a>
    </div>
    <div class="clearfix"></div>
    <div>
        <table id="tabla-datos" border="1">
            <tr id="profe-title">
                <th>Departament</th><th>Nom</th><th>Horari</th><th>Fichajes</th><th>
            </tr>
            @foreach ($profes as $profe)
            <tr id="{{$profe->dni}}">
                <th>{{$profe->Departamento->depcurt}}</th>
                <th>{{$profe->apellido1}} {{$profe->apellido2}}, {{$profe->nombre}}</th>
                <td></td><td></td>
            </tr>
            @endforeach     
        </table>
    </div>
    <div>
        <a class="pull-left prev-week">Semana anterior</a>
        <a class="pull-right next-week">Semana siguiente</a>
    </div>
</div>
@endsection
@section('scripts')
    {{ Html::script('/js/delete.js') }}
@endsection

