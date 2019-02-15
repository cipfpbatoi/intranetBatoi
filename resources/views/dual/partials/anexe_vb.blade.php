<div style="position:absolute;left:50%;margin-left:-631px;top:{{$top}}px;width:1283px;height:882px;border-style:outset;overflow:visible">
    <div style="position:absolute;left:0px;top:0px"><img src="{{url($imagen)}}" width=1283 height=882></div>
    <div style="position:absolute;left:263.20px;top:177.70px;width: 400px" >
        <span>{{$datosInforme['secretario']}}</span>
    </div>
    <div style="position:absolute;left:63.20px;top:197.70px;width: 400px" >
        <span>{{$datosInforme['centro']}}</span>
    </div>
    <div style="position:absolute;left:753.20px;top:197.70px;width: 400px" >
        <span>{{$datosInforme['codigo']}}</span>
    </div>
    <div style="position:absolute;left:63.20px;top:230.70px;width: 400px" >
        <span>{{$todos->Dual->Colaboracion->Ciclo->vliteral}}</span>
    </div>
    <div style="position:absolute;left:793.20px;top:230.70px;width: 400px" >
        <span>{{Curso()}}</span>
    </div>
    
    <div style="position:absolute;left:263.20px;top:290.70px;width: 400px" >
        <span>{{$datosInforme['secretario']}}</span>
    </div>
    <div style="position:absolute;left:63.20px;top:310.70px;width: 400px" >
        <span>{{$datosInforme['centro']}}</span>
    </div>
    <div style="position:absolute;left:753.20px;top:310.70px;width: 400px" >
        <span>{{$datosInforme['codigo']}}</span>
    </div>
    <div style="position:absolute;left:63.20px;top:343.70px;width: 400px" >
        <span>{{$todos->Dual->Colaboracion->Ciclo->cliteral}}</span>
    </div>
    <div style="position:absolute;left:753.20px;top:343.70px;width: 400px" >
        <span>{{Curso()}}</span>
    </div>
    <div style="position:absolute;left:43.20px;top:506.70px;width: 200px;font-size: small" >
        <span>{{$todos->Dual->Colaboracion->Centro->nombre}}</span>
    </div>
    <div style="position:absolute;left:283.20px;top:506.70px;width: 200px;font-size: small" >
        <span>{{$todos->Dual->Colaboracion->Centro->direccion}}</span>
    </div>
    <div style="position:absolute;left:563.20px;top:506.70px;width: 200px;font-size: small" >
        <span>{{$todos->Dual->Instructor->nombre}}</span>
    </div>
    <div style="position:absolute;left:893.20px;top:506.70px;width: 200px;font-size: small" >
        <span>{{$todos->Dual->Instructor->dni}}</span>
    </div>
    
    <div style="position:absolute;left:1043.20px;top:506.70px;width: 400px" >
        <span>{{$todos->Dual->Alumnos->count()}}</span>
    </div>
    <div style="position:absolute;left:1163.20px;top:506.70px;width: 400px" >
        <span>{{$todos->Dual->AlFct->horas}}</span>
    </div>
    <div style="position:absolute;left:463.20px;top:569.70px;width: 400px" >
        <span>{{$datosInforme['poblacion']}}</span>
    </div>
    <div style="position:absolute;left:650.20px;top:569.70px;width: 400px" >
        <span>{{day($datosInforme['date'])}}</span>
    </div>
    <div style="position:absolute;left:710.20px;top:569.70px;width: 400px" >
        <span>{{month($datosInforme['date'])}}</span>
    </div>
    <div style="position:absolute;left:860.20px;top:569.70px;width: 400px" >
        <span>{{year($datosInforme['date'])}}</span>
    </div>
    <div style="position:absolute;left:293.20px;top:749.70px;width: 400px" >
        <span>{{$datosInforme['director']}}</span>
    </div>
    <div style="position:absolute;left:853.20px;top:749.70px;width: 400px" >
        <span>{{$datosInforme['secretario']}}</span>
    </div>
</div>

   