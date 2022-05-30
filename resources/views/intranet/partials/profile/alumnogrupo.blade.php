@foreach ($panel->getElementos($pestana)->sortBy('subGrupo')->sortBy('posicion') as $elemento)
    @isset($elemento->Alumno)
        @php($alumno = $elemento->Alumno)
        <div class="col-md-4 col-sm-4 col-xs-12 profile_details">
            <div id="{{$alumno->nia}}" class="well profile_view">
                <div class="col-sm-12">
                    <h4 class="brief"><em>Nia : {{ $alumno->nia}}</em> <em class="fa fa-credit-card"> {{ $alumno->dni}}</em></h4>
                    <div class="left col-xs-7">
                        <h2>{{ $alumno->fullName }} </h2>
                        @if($alumno->expediente != ' ')
                            <p><strong>{{ trans("validation.attributes.expediente") }} {{$alumno->expediente}}</strong> </p>
                        @endif
                        <ul class="list-unstyled">
                            <li><em class="fa fa-phone"></em> {{$elemento->telef1}}</li>
                            @if ($elemento->telef2 != " ")
                            <li><em class="fa fa-phone"></em> {{$elemento->telef2}}</li>
                            @endif
                        </ul>
                    </div>
                    <div class="right col-xs-5 text-center">
                        <img src="{{asset('storage/'.$alumno->foto)}}" alt="" style="width: 90px; height: 100px" class="img-circle img-responsive">
                    </div>
                    <div class="left col-xs-12">
                        <ul class="list-unstyled">
                            <li><em class="fa fa-building"></em> {{$alumno->domicilio}} </li>
                            <li><em class="fa fa-envelope-o"></em> {{$alumno->codigo_postal}}@isset($alumno->Municipio){{$alumno->Municipio->municipio}}@endisset</li>
                            @if ($elemento->email != " ")
                            <li><em class="fa fa-envelope"></em> {{$alumno->email}}</li>
                            @endif
                        </ul>
                    </div>

                </div>

                <div class="col-xs-12 bottom text-center">
                    <div class="col-xs-12 col-sm-6 emphasis">
                        <p class="ratings">
                            <a>{{ $alumno->edat }} {{ trans("validation.attributes.años") }}</a>
                            @if ($alumno->repite == 0)
                                <a href="#"><span class="fa fa-star-o"></span></a>
                            @else
                                <a href="#"><span class="fa fa-star"></span></a>
                            @endif
                        </p>
                    </div>
                    <div class="col-xs-12 col-sm-6 emphasis">
                        @include ('intranet.partials.components.buttons',['tipo' => 'profile'])
                     </div>
                </div>
            </div>
        </div>
    @endisset
@endforeach

