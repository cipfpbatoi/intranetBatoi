<div class="col-md-4 col-sm-4 col-xs-12 profile_details" >
   <div id="{{$elemento->id}}" class="well profile_view">
        <div id="{{$fct->id}}" class="col-sm-12 fct">
            <div class="left col-md-9 col-xs-12">
                <h5>
                    {{$elemento->Centro->nombre}} <strong>({{$elemento->puestos}})</strong>
                </h5>
                @isset (authUser()->emailItaca)
                    <ul class="list-unstyled">
                        @if ($fct->Instructor)
                            <li>{{$fct->Instructor->nombre}}</li>
                            <li>{{$fct->Instructor->telefono}}</li>
                            <li>{{$fct->Instructor->email}}</li>
                        @else
                            <li>No hi ha instructor. Cal corregir el problema</li>
                        @endif
                        <li class="nombre">
                            {{isset($elemento->propietario->fullName)?$elemento->propietario->fullName:$elemento->tutor}}
                        </li>
                    </ul>
                @else
                    <ul class="list-unstyled">
                        <li><em class="fa fa-group"></em> {{$elemento->puestos}} lloc(s) de treball</li>
                        <li><em class="fa fa-clock-o"></em> {{$elemento->Centro->horarios}}</li>
                        <li><em class="fa fa-map-marker"></em> {{$elemento->Centro->direccion}}</li>
                        <li><em class="fa fa-folder"></em> {{$elemento->Centro->Empresa->actividad}}</li>
                        <li><em class="fa fa-envelope"></em> {{$elemento->Centro->Empresa->email}}</li>
                    </ul>
                @endisset
            </div>
            <div class="col-md-3 listActivity">
                @isset (authUser()->emailItaca)
                    @foreach ($contactos as $contacto)
                        <small>
                           {!! $contacto->render() !!}
                        </small>
                        <br/>
                    @endforeach
                @endisset
            </div>
        </div>
        <div class="col-xs-12 bottom text-center">
            <div class="col-xs-12 col-sm-5 emphasis">
                <p class="ratings">
                    {{strtoupper($elemento->Centro->localidad)}}<br/>
                </p>
                @isset (authUser()->emailItaca)
                    <a href="/colaboracion/{{$elemento->id}}/show" class="btn-success btn btn-xs">
                        <em class="fa fa-eye"></em>
                    </a>
                    @if (count($alumnos))
                        <em class="btn-success btn btn-xs">{{count($alumnos)}}</em>
                    @else
                        <a href="/fct/{{$fct->id}}/delete" class="btn-success btn btn-xs"><em class="fa fa-trash"></em></a>
                    @endif
                    <em class="fa fa-plus btn-success btn btn-xs" data-toggle="modal" data-target="#AddAlumno"></em>
                @endisset
            </div>
            <div class="col-xs-12 col-sm-7 emphasis">
                @isset (authUser()->emailItaca)
                    @include ('intranet.partials.components.buttons',['tipo' => 'profile'])<br/>
                    @php $elemento = $fct; @endphp
                    @include ('intranet.partials.components.buttons',['tipo' => 'fct'])
                @endisset
            </div>
        </div>
    </div>
</div>
