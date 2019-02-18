<div style="position:absolute;left:50%;margin-left:-691px;top:{{$top}}px;width:1383px;height:962px;border-style:outset;overflow:visible">
    <div style="position:absolute;left:0px;top:0px"><img src="{{url($imagen)}}" width=1383 height=962></div>
    <div style="position:absolute;left:273.20px;top:162.70px;width: 400px" >
        <span>{{$datosInforme['secretario']}}</span>
    </div>
    <div style="position:absolute;left:73.20px;top:182.70px;width: 400px" >
        <span>{{$datosInforme['centro']}}</span>
    </div>
    <div style="position:absolute;left:733.20px;top:182.70px;width: 400px" >
        <span>{{$datosInforme['codigo']}}</span>
    </div>
    <div style="position:absolute;left:73.20px;top:201.70px;width: 400px" >
        <span>{{$todos->Dual->Colaboracion->Centro->nombre}}</span>
    </div>
    <div style="position:absolute;left:753.20px;top:201.70px;width: 400px" >
        <span>{{$todos->Dual->Colaboracion->Centro->Empresa->cif}}</span>
    </div>
    <div style="position:absolute;left:553.20px;top:220.70px;width: 400px" >
        <span>{{Curso()}}</span>
    </div>
    
    <div style="position:absolute;left:273.20px;top:260.70px;width: 400px" >
        <span>{{$datosInforme['secretario']}}</span>
    </div>
    <div style="position:absolute;left:73.20px;top:280.70px;width: 400px" >
        <span>{{$datosInforme['centro']}}</span>
    </div>
    <div style="position:absolute;left:803.20px;top:280.70px;width: 400px" >
        <span>{{$datosInforme['codigo']}}</span>
    </div>
    <div style="position:absolute;left:73.20px;top:302.70px;width: 400px" >
        <span>{{$todos->Dual->Colaboracion->Centro->nombre}}</span>
    </div>
    <div style="position:absolute;left:793.20px;top:302.70px;width: 400px" >
        <span>{{$todos->Dual->Colaboracion->Centro->Empresa->cif}}</span>
    </div>
    <div style="position:absolute;left:589.20px;top:323.70px;width: 400px" >
        <span>{{Curso()}}</span>
    </div>
    <div style="position:absolute;left:103.20px;top:414.70px;width: 400px" >
        <span>{{$todos->Dual->Colaboracion->Centro->direccion}}</span>
    </div>
    <div style="position:absolute;left:593.20px;top:414.70px;width: 400px" >
        <span>{{$todos->Dual->Colaboracion->Centro->localidad}}</span>
    </div>
    <div style="position:absolute;left:893.20px;top:414.70px;width: 400px" >
        <span>{{$todos->Dual->Alumnos->count()}}</span>
    </div>
    <div style="position:absolute;left:1093.20px;top:414.70px;width: 400px" >
        <span>{{$todos->Dual->AlFct->horas}}</span>
    </div>
    <div style="position:absolute;left:483.20px;top:664.70px;width: 400px" >
        <span>{{$datosInforme['poblacion']}}</span>
    </div>
    <div style="position:absolute;left:690.20px;top:664.70px;width: 400px" >
        <span>{{day($datosInforme['date'])}}</span>
    </div>
    <div style="position:absolute;left:750.20px;top:664.70px;width: 400px" >
        <span>{{month($datosInforme['date'])}}</span>
    </div>
    <div style="position:absolute;left:900.20px;top:664.70px;width: 400px" >
        <span>{{year($datosInforme['date'])}}</span>
    </div>
    <div style="position:absolute;left:313.20px;top:844.70px;width: 400px" >
        <span>{{$datosInforme['director']}}</span>
    </div>
    <div style="position:absolute;left:883.20px;top:844.70px;width: 400px" >
        <span>{{$datosInforme['secretario']}}</span>
    </div>
</div>