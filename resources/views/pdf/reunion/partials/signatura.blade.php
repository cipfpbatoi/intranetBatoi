<div class="container" style="clear: both">
    <br/>
    @if (file_exists(storage_path().'/app/public/signatures/'.$datosInforme->Responsable->dni.'.png'))
        @php($ruta = public_path('/storage/signatures/'.$datosInforme->Responsable->dni.'.png'))
        <div style="width:100%;float:left">
            <img style="width:260px;heigth:220px" src="{{$ruta}}" alt="Signatura:{{$ruta}}"/>
        </div>
    @endif
    <div style="width:50%;float:left">SIGNAT: {{$datosInforme->Responsable->nombre}}  {{$datosInforme->Responsable->apellido1}}  {{$datosInforme->Responsable->apellido2}}</div>
    <div style="width:50%;float:right;text-align: right">{{strtoupper(config('contacto.poblacion'))}} A {{$datosInforme->hoy}}</div>
</div>

