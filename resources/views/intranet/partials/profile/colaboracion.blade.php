@foreach ($panel->getElementos($pestana) as $elemento)
    <div class="col-md-4 col-sm-4 col-xs-12 profile_details">
        <div id="{{$elemento->id}}" class="well profile_view">
            <div class="col-sm-12">
                <div class="left col-md-8 col-xs-12">
                    <h5>{{$elemento->Centro->nombre}} <strong>({{$elemento->puestos}})</strong></h5>
                    <ul class="list-unstyled">
                        <li>{{$elemento->contacto}}</li>
                        <li>{{$elemento->telefono}}</li>
                        <li>{{$elemento->email}}</li>
                        <li class="nombre">{{isset($elemento->propietario->fullName)?$elemento->propietario->fullName:$elemento->tutor}}
                        </li>
                    </ul>
                </div>
                <div class="col-md-4 listActivity">
                    @php
                        $contactCol = \Intranet\Entities\Activity::mail('Colaboracion')->id($elemento->id)->orderBy('created_at')->get();
                        $fcts = \Intranet\Entities\Fct::where('idColaboracion',$elemento->id)->where('asociacion',1)->get();
                        $contactFct = \Intranet\Entities\Activity::mail('Fct')->ids(hazArray($fcts,'id','id'))->get();
                        $alumnos = [];
                        foreach ($fcts as $fct)
                            $alumnos = array_merge($alumnos,hazArray($fct->Alumnos,'nia','nia'));
                        $contactAl = \Intranet\Entities\Activity::mail('Alumno')->ids($alumnos)->get();
                    @endphp
                    @foreach ($contactCol as $contacto)
                        <small>{{firstWord($contacto->comentari)}}-{{fechaCurta($contacto->created_at)}}</small><br/>
                    @endforeach
                    @foreach ($contactFct as $contacto)
                        <small>{{firstWord($contacto->comentari)}}-{{fechaCurta($contacto->created_at)}}</small><br/>
                    @endforeach
                    @foreach ($contactAl as $contacto)
                        <small>{{firstWord($contacto->comentari)}}-{{fechaCurta($contacto->created_at)}}</small><br/>
                    @endforeach
                </div>
            </div>
            <div class="col-xs-12 bottom text-center">
                <div class="col-xs-12 col-sm-3 emphasis">
                    <p class="ratings">
                        {{$elemento->Centro->localidad}}<br/>
                    </p>
                </div>
                <div class="col-xs-12 col-sm-9 emphasis">
                    @include ('intranet.partials.buttons',['tipo' => 'profile'])<br/>
                    @include ('intranet.partials.buttons',['tipo' => 'infile'])
                </div>
            </div>
        </div>
    </div>
@endforeach
