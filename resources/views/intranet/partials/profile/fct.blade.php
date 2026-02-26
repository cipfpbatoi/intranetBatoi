@foreach ($panel->getElementos($pestana) as $fct)
    <div class="col-md-4 col-sm-4 col-xs-12 profile_details" >
        <div id="{{$fct->id}}" class="well profile_view">
            <div id="{{$fct->id}}" class="col-sm-12 fct">
                <div class="left col-md-6 col-xs-12">
                    <h5>
                        {{ optional(optional($fct->Colaboracion)->Centro)->nombre ?? 'Sense centre' }}
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
                    @foreach($panel->getBotones('profile') as $button)
                        {!! $button->show($fct) !!}
                    @endforeach
                </div>
                @if ($fct->asociacion === 3) <h5>-DUAL-</h5> @endif
                <div class="col-md-6 listActivity">
                    @isset (authUser()->emailItaca)
                        @foreach ($fct->Contactos as $contacto)
                            <x-activity :activity="$contacto" />
                            <br/>
                        @endforeach
                    @endisset
                </div>
            </div>
            <div class="col-xs-12 bottom text-center"
                 @if ($fct->asociacion === 3) style="background-color:orange"@endif
            >
                <div class="col-xs-12 col-sm-5 emphasis">
                    <p class="ratings">
                        {{ strtoupper(optional(optional($fct->Colaboracion)->Centro)->localidad ?? '') }}<br/>
                    </p>
                    <em class="btn-success btn btn-xs">{{count($fct->Alumnos)}}</em>
                    <a href="{{ route('fct.show', ['id' => $fct->id]) }}" class="btn-success btn btn-xs" title="Mostrar Fct">
                        <i class="fa fa-eye"></i>
                    </a>
                    <a href="{{ route('PanelColaboracion.colaboracion', ['id' => $fct->id, 'documento' => 'finEmpresa']) }}"
                       class="btn-success btn btn-xs"
                       title="Enviar Correu">
                        <i class="fa fa-envelope-o"></i>
                    </a>
                </div>
                <div class="col-xs-12 col-sm-7 emphasis">
                    <x-botones :panel="$panel" tipo="fct" :elemento="$elemento ?? null"/>
                 </div>
            </div>
        </div>
    </div>
@endforeach
