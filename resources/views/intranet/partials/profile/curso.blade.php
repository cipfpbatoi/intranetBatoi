@foreach ($panel->getElementos($pestana) as $elemento)
<div class="col-md-4 col-sm-4 col-xs-12 profile_details">
    <div id="{{$elemento->id}}" class="well profile_view">
        <div class="col-sm-12">
            <h4 class="brief">
                <i class="fa fa-calendar"></i> {{$elemento->fecha_inicio}} - {{ $elemento->fecha_fin }} <br/>
                <i class="fa fa-calendar-o"></i> {{$elemento->hora_ini}} - {{$elemento->hora_fin}}<br/>
                @if ($elemento->horas > 0)
                <i class="fa fa-hourglass"></i> {{$elemento->horas}} {{ trans('messages.generic.horas')}}
                @endif
                <i class="fa fa-building"></i> {{$elemento->NAlumnos}}
                @if ($elemento->aforo > 0)
                    / {{$elemento->aforo}}
                @endif
            </h4>
            <div class="left col-xs-12">
                <h5>{{$elemento->titulo}} </h5>
                <ul class="list-unstyled">
                    @if (strlen($elemento->profesorado))
                        <li><i class="fa fa-group"></i> {{$elemento->profesorado}}</li>
                    @endif 
                    @if (strlen($elemento->comentarios))
                    <li><i class="fa fa-text-width"></i> {{ $elemento->comentarios}} </li>
                    @endif
                </ul>
            </div>
        </div>
        <div class="col-xs-12 bottom text-center">
            <div class="col-xs-12 col-sm-4 emphasis">
                <p class="ratings">
                    @if ($elemento->aforo == 0) <a href='#' class='btn btn-success btn-xs' > {{ trans('messages.buttons.disponible') }}  </a>
                    @else  
                        @if ($elemento->aforo > $elemento->NAlumnos) <a href='#' class='btn btn-success btn-xs' > {{ trans('messages.buttons.disponible') }}  </a>
                        @else <a href='#' class='btn btn-danger btn-xs' > {{ trans('messages.buttons.nodisponible') }} </a>
                        @endif
                    @endif   
                    @if ($elemento->Registrado()) <a href='#' class='btn btn-success btn-xs' > {{ trans('messages.buttons.registered') }}  </a> @endif
                </p>
            </div>
            <div class="col-xs-12 col-sm-8 emphasis">
                @include ('intranet.partials.buttons',['tipo' => 'profile'])
            </div>
        </div>
    </div>
</div>
@endforeach
@include('intranet.partials.newModal.explicacion')