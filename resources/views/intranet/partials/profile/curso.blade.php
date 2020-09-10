@foreach ($panel->getElementos($pestana) as $elemento)
<div class="col-md-4 col-sm-4 col-xs-12 profile_details">
    <div id="{{$elemento->id}}" class="well profile_view">
        <div class="col-sm-12">
            <h4 class="brief">
                <em class="fa fa-calendar"></em> {{$elemento->fecha_inicio}} - {{ $elemento->fecha_fin }} <br/>
                <em class="fa fa-calendar-o"></em> {{$elemento->hora_ini}} - {{$elemento->hora_fin}}<br/>
                @if ($elemento->horas > 0)
                <em class="fa fa-hourglass"></em> {{$elemento->horas}} {{ trans('messages.generic.horas')}}
                @endif
                <em class="fa fa-building"></em> {{$elemento->NAlumnos}}
                @if ($elemento->aforo > 0)
                    / {{$elemento->aforo}}
                @endif
            </h4>
            <div class="left col-xs-12">
                <h5>{{$elemento->titulo}} </h5>
                <ul class="list-unstyled">
                    @if (strlen($elemento->profesorado))
                        <li><em class="fa fa-group"></em> {{$elemento->profesorado}}</li>
                    @endif 
                    @if (strlen($elemento->comentarios))
                    <li><em class="fa fa-text-width"></em> {{ $elemento->comentarios}} </li>
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
@include('intranet.partials.modal.explicacion')