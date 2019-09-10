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
        <div style="float:left;width:23%;margin-right:10px;height:160px "  >
            @if ($elemento->foto)
            <img src="{{url($elemento->foto)}}" style="width:125px;height:125px;margin-left:20;border:thin #000 solid;" /><br/>
            @else
            <img src="" style="width:125px;height:125px;margin-left:20;border:thin #000 solid;" /><br/>
            @endif
            <h6 style="font-size:xx-small;text-align: center">{{$elemento->nombre.' '.$elemento->apellido1.' '.$elemento->apellido2}} </h6>
        </div>
    @endforeach
</div>
@endsection

