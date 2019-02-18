<div style="position:absolute;left:50%;margin-left:-691px;top:{{$top}}px;width:1383px;height:962px;border-style:outset;overflow:visible">
    <div style="position:absolute;left:0px;top:0px"><img src="{{url($imagen)}}" width=1383 height=962></div>
   <div style="position:absolute;left:263.20px;top:195.70px;width: 400px" >
        <span>{{$datosInforme['secretario']}}</span>
    </div>
    <div style="position:absolute;left:63.20px;top:217.70px;width: 400px" >
        <span>{{$datosInforme['centro']}}</span>
    </div>
    <div style="position:absolute;left:803.20px;top:217.70px;width: 400px" >
        <span>{{$datosInforme['codigo']}}</span>
    </div>
    <div style="position:absolute;left:63.20px;top:253.70px;width: 400px" >
        <span>{{$todos->Dual->Colaboracion->Ciclo->vliteral}}</span>
    </div>
    <div style="position:absolute;left:853.20px;top:253.70px;width: 400px" >
        <span>{{Curso()}}</span>
    </div>
    
    <div style="position:absolute;left:263.20px;top:320.70px;width: 400px" >
        <span>{{$datosInforme['secretario']}}</span>
    </div>
    <div style="position:absolute;left:63.20px;top:340.70px;width: 400px" >
        <span>{{$datosInforme['centro']}}</span>
    </div>
    <div style="position:absolute;left:813.20px;top:340.70px;width: 400px" >
        <span>{{$datosInforme['codigo']}}</span>
    </div>
    <div style="position:absolute;left:63.20px;top:377.70px;width: 400px" >
        <span>{{$todos->Dual->Colaboracion->Ciclo->cliteral}}</span>
    </div>
    <div style="position:absolute;left:803.20px;top:377.70px;width: 400px" >
        <span>{{Curso()}}</span>
    </div>
    <div style="position:absolute;left:43.20px;top:550.70px;width: 200px;font-size: small" >
        <span>{{$todos->Dual->Colaboracion->Centro->nombre}}</span>
    </div>
    <div style="position:absolute;left:303.20px;top:550.70px;width: 200px;font-size: small" >
        <span>{{$todos->Dual->Colaboracion->Centro->direccion}}</span>
    </div>
    <div style="position:absolute;left:603.20px;top:550.70px;width: 200px;font-size: small" >
        <span>{{$todos->Dual->Instructor->nombre}}</span>
    </div>
    <div style="position:absolute;left:963.20px;top:550.70px;width: 200px;font-size: small" >
        <span>{{$todos->Dual->Instructor->dni}}</span>
    </div>
    
    <div style="position:absolute;left:1113.20px;top:550.70px;width: 400px" >
        <span>{{$todos->Dual->Alumnos->count()}}</span>
    </div>
    <div style="position:absolute;left:1253.20px;top:550.70px;width: 400px" >
        <span>{{$todos->Dual->AlFct->horas}}</span>
    </div>
    <div style="position:absolute;left:463.20px;top:629.70px;width: 400px" >
        <span>{{$datosInforme['poblacion']}}</span>
    </div>
    <div style="position:absolute;left:660.20px;top:629.70px;width: 400px" >
        <span>{{day($datosInforme['date'])}}</span>
    </div>
    <div style="position:absolute;left:740.20px;top:629.70px;width: 400px" >
        <span>{{month($datosInforme['date'])}}</span>
    </div>
    <div style="position:absolute;left:890.20px;top:629.70px;width: 400px" >
        <span>{{year($datosInforme['date'])}}</span>
    </div>
    <div style="position:absolute;left:293.20px;top:809.70px;width: 400px" >
        <span>{{$datosInforme['director']}}</span>
    </div>
    <div style="position:absolute;left:853.20px;top:809.70px;width: 400px" >
        <span>{{$datosInforme['secretario']}}</span>
    </div>
</div>

   