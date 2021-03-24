@extends('layouts.pdf')
@section('css')
    {{ Html::style('/css/dual.css') }}
@endsection
@section('content')
    <div style="position:absolute;margin-left:0px;top:0px;width:1488px;height:2104px;overflow:visible">
        <div style="position:absolute;left:0px;top:0px">
            <img src="{{public_path('img/pdf/dual/anexe_v.jpg')}}" width=1488px height=2104px>
        </div>
        <div style="font-size:1.5em;position:absolute;left:223.20px;top:596.70px;width: 400px" >
            <span>{{$datosInforme['secretario']}}</span>
        </div>
        <div style="font-size:1.5em;position:absolute;left:303.20px;top:627.70px;width: 400px" >
            <span>{{$datosInforme['centro']}}</span>
        </div>
        <div style="font-size:1.5em;position:absolute;left:633.20px;top:649.70px;width: 400px" >
            <span>{{$datosInforme['poblacion']}}</span>
        </div>
        <div style="font-size:1.5em;position:absolute;left:200.20px;top:698.70px;width: 400px" >
            <span>{{$todos->Fct->Colaboracion->Ciclo->tipo == 1?'Mitjà':'Superior'}}</span>
        </div>
        <div style="position:absolute;left:100.20px;top:728.70px;width: 900px" >
            <span>{{$todos->Fct->Colaboracion->Ciclo->titol}}</span>
        </div>
        <div style="position:absolute;left:380.20px;top:750.70px;width: 900px" >
            <span>{{explode('i',$todos->Fct->Colaboracion->Ciclo->rd)[0]}}</span>
        </div>
        <div style="position:absolute;left:80.20px;top:780.70px;width: 900px" >
            <span>{{explode('i',$todos->Fct->Colaboracion->Ciclo->rd)[1]}}</span>
        </div>
        <div style="font-size:1.5em;position:absolute;left:280.20px;top:900.70px;width: 900px" >
            <span>{{ $todos->Alumno->FullName }}</span>
        </div>
        <div style="font-size:1.5em;position:absolute;left:280.20px;top:925.70px;width: 900px" >
            <span>{{ $todos->Alumno->dni }}</span>
        </div>
        <div style="font-size:1.25em;position:absolute;left:460.20px;top:1005.70px;width: 900px" >
            <span>Formació i Orientació Laboral</span>
        </div>
        <div style="font-size:1.5em;position:absolute;left:592.20px;top:1030.70px;width: 900px" >
            <span>96</span>
        </div>
        <div style="font-size:1.5em;position:absolute;left:180.20px;top:1105.70px;width: 900px" >
            <span>{{config('contacto.poblacion')}}</span>
        </div>
        <div style="font-size:1.5em;position:absolute;left:180.20px;top:1105.70px;width: 900px" >
            <span>{{config('contacto.poblacion')}}</span>
        </div>
        <div style="font-size:1.5em;position:absolute;left:180.20px;top:1105.70px;width: 900px" >
            <span>{{config('contacto.poblacion')}}</span>
        </div>
        <div style="font-size:1.5em;position:absolute;left:315.20px;top:1105.70px;width: 900px" >
            <span>{{day($datosInforme['date'])}}</span>
        </div>
        <div style="font-size:1.5em;position:absolute;left:395.20px;top:1105.70px;width: 900px" >
            <span>{{month($datosInforme['date'])}}</span>
        </div>
        <div style="font-size:1.5em;position:absolute;left:560.20px;top:1105.70px;width: 900px" >
            <span>{{year($datosInforme['date'])}}</span>
        </div>
        <div style="font-size:1.25em;position:absolute;left:175.20px;top:1230.70px;width: 900px" >
            <span>{{$datosInforme['director']}}</span>
        </div>
        <div style="font-size:1.25em;position:absolute;left:480.20px;top:1230.70px;width: 900px" >
            <span>{{$datosInforme['secretario']}}</span>
        </div>

        <div style="font-size:1.5em;position:absolute;left:923.20px;top:596.70px;width: 400px" >
            <span>{{$datosInforme['secretario']}}</span>
        </div>
        <div style="font-size:1.5em;position:absolute;left:1063.20px;top:627.70px;width: 400px" >
            <span>{{$datosInforme['centro']}}</span>
        </div>
        <div style="font-size:1.5em;position:absolute;left:833.20px;top:669.70px;width: 400px" >
            <span>{{$datosInforme['poblacion']}}</span>
        </div>
        <div style="font-size:1.5em;position:absolute;left:900.20px;top:698.70px;width: 400px" >
            <span>{{$todos->Fct->Colaboracion->Ciclo->tipo == 1?'Medio':'Superior'}}</span>
        </div>
        <div style="position:absolute;left:900.20px;top:728.70px;width: 900px" >
            <span>{{$todos->Fct->Colaboracion->Ciclo->titol}}</span>
        </div>
        <div style="position:absolute;left:1200.20px;top:750.70px;width: 900px" >
            <span>{{explode('i',$todos->Fct->Colaboracion->Ciclo->rd)[0]}}</span>
        </div>
        <div style="position:absolute;left:850.20px;top:780.70px;width: 900px" >
            <span>{{explode('i',$todos->Fct->Colaboracion->Ciclo->rd)[1]}}</span>
        </div>
        <div style="font-size:1.5em;position:absolute;left:1000.20px;top:900.70px;width: 900px" >
            <span>{{ $todos->Alumno->FullName }}</span>
        </div>
        <div style="font-size:1.5em;position:absolute;left:1020.20px;top:925.70px;width: 900px" >
            <span>{{ $todos->Alumno->dni }}</span>
        </div>
        <div style="font-size:1.25em;position:absolute;left:750.20px;top:1030.70px;width: 900px" >
            <span>Formació i Orientació Laboral</span>
        </div>
        <div style="font-size:1.5em;position:absolute;left:890.20px;top:1050.70px;width: 900px" >
            <span>96</span>
        </div>
        <div style="font-size:1.5em;position:absolute;left:880.20px;top:1105.70px;width: 900px" >
            <span>{{config('contacto.poblacion')}}</span>
        </div>
        <div style="font-size:1.5em;position:absolute;left:880.20px;top:1105.70px;width: 900px" >
            <span>{{config('contacto.poblacion')}}</span>
        </div>
        <div style="font-size:1.5em;position:absolute;left:880.20px;top:1105.70px;width: 900px" >
            <span>{{config('contacto.poblacion')}}</span>
        </div>
        <div style="font-size:1.5em;position:absolute;left:1015.20px;top:1105.70px;width: 900px" >
            <span>{{day($datosInforme['date'])}}</span>
        </div>
        <div style="font-size:1.5em;position:absolute;left:1095.20px;top:1105.70px;width: 900px" >
            <span>{{month($datosInforme['date'])}}</span>
        </div>
        <div style="font-size:1.5em;position:absolute;left:1260.20px;top:1105.70px;width: 900px" >
            <span>{{year($datosInforme['date'])}}</span>
        </div>
        <div style="font-size:1.25em;position:absolute;left:855.20px;top:1230.70px;width: 900px" >
            <span>{{$datosInforme['director']}}</span>
        </div>
        <div style="font-size:1.25em;position:absolute;left:1180.20px;top:1230.70px;width: 900px" >
            <span>{{$datosInforme['secretario']}}</span>
        </div>
    </div>
@endsection