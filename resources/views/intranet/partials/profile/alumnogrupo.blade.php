@foreach ($panel->getElementos($pestana)->sortBy('subGrupo')->sortBy('posicion') as $elemento)
    @isset($elemento->Alumno)
        @php($alumno = $elemento->Alumno)
        <div class="col-md-4 col-sm-4 col-xs-12 profile_details">
            <div id="{{$alumno->nia}}" class="well profile_view">
                <div class="col-sm-12">
                    <h4 class="brief"><i>Nia : {{ $alumno->nia}}</i> <i class="fa fa-credit-card"> {{ $alumno->dni}}</i></h4>
                    <div class="left col-xs-7">
                        <h2>{{ $alumno->fullName }} </h2>
                        @if($alumno->expediente != ' ')
                            <p><strong>{{ trans("validation.attributes.expediente") }} {{$alumno->expediente}}</strong> </p>
                        @endif
                        <ul class="list-unstyled">
                            <li><i class="fa fa-phone"></i> {{$elemento->telef1}}</li>
                            @if ($elemento->telef2 != " ")
                            <li><i class="fa fa-phone"></i> {{$elemento->telef2}}</li>
                            @endif
                        </ul>
                    </div>
                    <div class="right col-xs-5 text-center">
                        <img src="{{asset('storage/'.$alumno->foto)}}" alt="" heigth="100px" width="90px" class="img-circle img-responsive">
                    </div>
                    <div class="left col-xs-12">
                        <ul class="list-unstyled">
                            <li><i class="fa fa-building"></i> {{$alumno->domicilio}} </li>
                            <li><i class="fa fa-envelope-o"></i> {{$alumno->codigo_postal}} {{$alumno->Municipio->municipio}}</li>
                            @if ($elemento->email != " ")
                            <li><i class="fa fa-envelope"></i> {{$alumno->email}}</li>
                            @endif
                        </ul>
                    </div>

                </div>

                <div class="col-xs-12 bottom text-center">
                    <div class="col-xs-12 col-sm-6 emphasis">
                        <p class="ratings">
                            @php($nac = new Jenssegers\Date\Date($alumno->fecha_nac))
                            <a>{{ $nac->age }} {{ trans("validation.attributes.años") }}</a>
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

