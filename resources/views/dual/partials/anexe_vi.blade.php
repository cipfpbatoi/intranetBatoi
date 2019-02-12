<div style="position:absolute;left:50%;margin-left:-600px;top:{{$top}}px;width:1200px;height:1700px;border: 0px">
    <div style="position:absolute;left:0px;top:0px"><img src="{{url($imagen)}}" width=1200 height=1700></div>
    
    <div style="position:absolute;left:65.70px;top:250.40px">
        <span style="font-weight: bold"> {{config('contacto.nombre')}}</span>
    </div>
    <div style="position:absolute;left:595.70px;top:250.40px">
        <span style="font-weight: bold"> {{config('contacto.codi')}}</span>
    </div>
    <div style="position:absolute;left:821.70px;top:232.40px">
        <span style="font-weight: bold">X</span>
    </div>
    <div style="position:absolute;left:975.70px;top:232.40px">
        <span style="font-weight: bold">X</span>
    </div>
    
    <div style="position:absolute;left:65.70px;top:300.40px">
        <span style="font-weight: bold"> {{AuthUser()->Departamento->literal}}</span>
    </div>
    <div style="position:absolute;left:425.70px;top:300.40px;width:400px ">
        <span style="font-weight: bold">{{ $todos->first()->Fct->Colaboracion->Ciclo->literal }}</span>
    </div>
    <div style="position:absolute;left:856.70px;top:288.40px">
        <span style="font-weight: bold">X</span>
    </div>
    <div style="position:absolute;left:1020.70px;top:310.40px;width: 400px">
        <span style="font-weight: bold">X</span>
    </div>
    
    <div style="position:absolute;left:65.70px;top:355.40px">
        <span style="font-weight: bold"> {{AuthUser()->fullName}}</span>
    </div>
    <div style="position:absolute;left:115.70px;top:468.40px;">
        <span style="font-weight: bold">{{$datosInforme['alumnos']}}</span>
    </div>
    <div style="position:absolute;left:230.70px;top:468.40px;">
        <span style="font-weight: bold">{{$datosInforme['alumnas']}}</span>
    </div>
    <div style="position:absolute;left:342.70px;top:468.40px;">
        <span style="font-weight: bold">{{$datosInforme['dualH']}}</span>
    </div>
    <div style="position:absolute;left:450.70px;top:468.40px;">
        <span style="font-weight: bold">{{$datosInforme['dualM']}}</span>
    </div>
    <div style="position:absolute;left:595.70px;top:468.40px;">
        <span style="font-weight: bold">{{$datosInforme['empresas']}}</span>
    </div>
    <div style="position:absolute;left:887.70px;top:468.40px;">
        <span style="font-weight: bold">{{$datosInforme['dualH']}}</span>
    </div>
    <div style="position:absolute;left:950.70px;top:468.40px;">
        <span style="font-weight: bold">{{$datosInforme['dualM']}}</span>
    </div>
    
</div>
