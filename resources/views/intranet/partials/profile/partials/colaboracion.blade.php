
<div class="col-md-4 col-sm-4 col-xs-12 profile_details">
    <div id="{{$elemento->id}}" class="well profile_view"
         @if ($elemento->estado == 3) style='border-color: #90111a;border-width: medium' @endif
    >
        <div class="col-sm-12">
            <div class="left col-md-9 col-xs-12">
                <h5>
                     {{$elemento->Centro->nombre}}
                </h5>
                @isset (authUser()->emailItaca)
                    <ul class="list-unstyled">
                        <li>Conveni: <strong>
                                {{$elemento->Centro->Empresa->concierto}}
                                @if ($elemento->Centro->Empresa->conveniCaducat)
                                    <em class="fa fa-hand-o-down"></em>
                                @else
                                    <em class="fa fa-hand-o-up"></em>
                                @endif
                            </strong>
                        </li>
                        <li><em class="fa fa-group"></em> {{$elemento->puestos}} lloc(s) de treball</li>
                        <li><em class="fa fa-user"></em> {{$elemento->contacto}}</li>
                        <li><em class="fa fa-phone"></em> {{$elemento->telefono}}</li>
                        <li><em class="fa fa-envelope"></em> {{$elemento->email}}</li>
                        <li><em class="fa fa-user-secret"></em> {{$elemento->profesor??'No assignada'}}</li>
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
             @foreach ($contactos as $contacto)
                <x-activity :activity="$contacto" />
                <br/>
            @endforeach
            @if(($elemento->relacionadas ?? collect())->isNotEmpty())
                <ul class="list-unstyled" style="margin-top:.5rem">
                    <li><span class="label label-default">Altres cicles (mateix centre/departament)</span></li>

                    @foreach ($elemento->relacionadas as $rel)
                        <li class="small" style="margin-top:.25rem">
                            <em class="fa fa-institution"></em>
                            {{ optional($rel->Ciclo)->literal ?? ($rel->idCiclo ?? $rel->ciclo_id) }}
                            — {{ optional($rel->Propietario)->shortName }}

                            @if(($rel->contactos ?? collect())->isNotEmpty())
                                @if ($rel->estado == 3)
                                    <span class="label label-danger">Contactada</span>
                                @endif
                                @if ($rel->estado == 2)
                                    <span class="label label-success">Contactada</span>
                                @endif
                                @if ($rel->estado == 1)
                                    <span class="label label-warning">Contactada</span>
                                @endif
                                <div class="mt-1">
                                    @foreach ($rel->contactos as $act)
                                        @if ($act->document === "Contacte previ")
                                            <div class="text-muted"
                                                 data-bs-toggle="tooltip"
                                                 data-bs-placement="top"
                                                 title=" {{ $act->comentari }}"
                                                 style="cursor:pointer;" >
                                                <em class="fa fa-commenting"></em>
                                                {{ $act->created_at?->format('d/m/Y H:i') }} 
                                                
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            @endif
                        </li>
                    @endforeach
                </ul>
            @endif
           
        </div>
        <div class="col-xs-12 bottom text-center">
            <div class="col-xs-12 col-sm-4 emphasis">
                <p class="ratings">
                    {{$elemento->localidad}}<br/>
                </p>
            </div>
            <div class="col-xs-12 col-sm-8 emphasis">
                @isset (authUser()->emailItaca)
                    @include ('intranet.partials.components.buttons',['tipo' => 'profile'])<br/>
                    @include ('intranet.partials.components.buttons',['tipo' => 'nofct'])
                @endisset
            </div>
        </div>
    </div>
</div>
