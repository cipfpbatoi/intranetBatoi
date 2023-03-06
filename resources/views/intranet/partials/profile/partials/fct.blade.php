<div class="col-md-4 col-sm-4 col-xs-12 profile_details" >
        <div id="{{$fct->id}}" class="col-sm-12 fct">
            <div class="left col-md-9 col-xs-12">
                <h5>
                    {{$fct->Colaboracion->Centro->nombre}} <strong>({{$fct->Colaboracion->puestos}})</strong>
                </h5>
                <ul class="list-unstyled">
                        @if ($fct->Instructor)
                            <li>{{$fct->Instructor->nombre}}</li>
                            <li>{{$fct->Instructor->telefono}}</li>
                            <li>{{$fct->Instructor->email}}</li>
                        @else
                            <li>No hi ha instructor. Cal corregir el problema</li>
                        @endif
                </ul>
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
                    {{strtoupper($fct->Colaboracion->Centro->localidad)}}<br/>
                </p>
                @isset (authUser()->emailItaca)
                    @if (count($alumnos))
                        <em class="btn-success btn btn-xs">{{count($alumnos)}}</em>
                    @else
                        <a href="/fct/{{$fct->id}}/delete" class="btn-success btn btn-xs"><em class="fa fa-trash"></em></a>
                    @endif
                @endisset
            </div>
            <div class="col-xs-12 col-sm-7 emphasis">
                @isset (authUser()->emailItaca)
                    @include ('intranet.partials.components.buttons',['tipo' => 'fct'])
                @endisset
            </div>
        </div>
</div>
