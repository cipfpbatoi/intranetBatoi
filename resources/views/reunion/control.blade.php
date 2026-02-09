@extends('layouts.intranet')
@section('css')
<title>@lang("models.Reunion.control")</title>
@endsection
@section('content')
<div id="app">
    <div class="clearfix col-md-6 col-lg-6">
        <form action='/direccion/reunion/aviso' method="POST">
            {{ csrf_field() }}
            <select id='tipo_id' name='tipo' class='form-control select' required >
                @foreach (config('auxiliares.reunionesControlables') as $index => $howMany )
                    <option value='{{$index}}'>{{Intranet\Services\Document\TipoReunionService::find($index)->vliteral}}</option>
                @endforeach
            </select>
            <select id='numero_id' name='numero' class='form-control select' >
                @foreach (Intranet\Services\Document\TipoReunionService::find(2)->numeracion as $index => $valor))
                    <option value='{{$index}}'>{{$valor}}</option>
                @endforeach
            </select>
            <select name='quien' class='form-control select' >
                <option value='0'>Tots els tutors</option>
                <option value='1'>Tutors 1er</option>
                <option value='2'>Tutors 2on</option>
            </select>
            <input type='submit' value='Avisar Falta Document' class="btn btn-primary"/>
        </form>
    </div>
    <br/><br/>
    <div class="col-md-12 col-lg-12">
        <table id="tabla-datos" border="1">
            <tr id="profe-title" >
                <th style="text-align: center">Grup</th><th style="text-align: center">Equip Educatiu</th><th style="text-align: center">Elecció Delegat</th>
                <th style="text-align: center">Pares</th><th style="text-align: center">Avaluació</th><th style="text-align: center">FSE</th>
            </tr>
            @foreach ($reuniones as $index => $xgrupo)
            <tr>
                <td>{{$index}}</td>
                @foreach ($xgrupo as $xtipo)
                <td style="padding: 3px">
                        @foreach ($xtipo as $reunion)
                        <a href='/reunion/{{$reunion->id}}/pdf'>{{substr($reunion->fecha,0,10)}} - {{ $reunion->Xnumero }}<br/></a>
                        @endforeach
                    </td>
                @endforeach
            </tr>
            @endforeach     
        </table>
    </div>
</div>
@endsection
@section('titulo')
@lang("models.Reunion.control")
@endsection
@section('scripts')
    {{ Html::script("/js/Reunion/control.js") }}
@endsection
