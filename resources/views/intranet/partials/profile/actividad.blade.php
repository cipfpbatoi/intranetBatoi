@foreach ($panel->getElementos($pestana) as $elemento)
<div class="col-md-4 col-sm-4 col-xs-12 profile_details">
    <div id="{{$elemento->id}}" class="well profile_view">
        <div class="col-sm-12">
            <h4 class="brief">
                @if (esMismoDia($elemento->desde,$elemento->hasta))
                <i class="fa fa-calendar"></i> {{$elemento->desde}} - {{ substr($elemento->hasta,11) }}
                @else
                <i class="fa fa-calendar"></i> {{$elemento->desde}} - <i class="fa fa-calendar"></i> {{$elemento->hasta}}
                @endif
            </h4>
            <div class="left col-xs-12">
                <h5>{{substr($elemento->name,0,140)}} 
                    @if (strlen($elemento->name)>140)
                    ... 
                    @endif 
                </h5>
                <ul class="list-unstyled">
                    @foreach ($elemento->profesores as $profesor)
                        <li><i class="fa fa-user"></i> {{$profesor->nombre}} {{$profesor->apellido1}}</li>
                    @endforeach
                    @foreach ($elemento->grupos as $grupo)
                    <li><i class="fa fa-group"></i> {{ $grupo->nombre}} </li>
                    @endforeach
                </ul>
            </div>
        </div>
        <div class="col-xs-12 bottom text-center">
            <div class="col-xs-12 col-sm-4 emphasis">
                <p class="ratings">
                    @if ($elemento->estraescolar == 1)
                        <a href='#' class='btn btn-success btn-xs' >@lang("messages.menu.Orientacion")</a>
                    @else
                        @if ($elemento->estado<2) <a href='#' class='btn btn-danger btn-xs' >
                        @else <a href='#' class='btn btn-success btn-xs' >   
                        @endif    
                        {{ $elemento->situacion }}</a>
                    @endif    
                </p>
            </div>
            <div class="col-xs-12 col-sm-8 emphasis">
                @include ('intranet.partials.buttons',['tipo' => 'profile'])
            </div>
        </div>
    </div>
</div>
@endforeach
