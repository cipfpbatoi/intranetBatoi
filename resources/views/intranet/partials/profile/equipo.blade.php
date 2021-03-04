@foreach ($panel->getElementos($pestana) as $elemento)
<div class="col-md-4 col-sm-4 col-xs-12 profile_details">
    <div id="{{$elemento->dni}}" class="well profile_view">
        <div class="col-sm-12">
            <h4 class="brief"><i>{{ $elemento->FullName }}</i></h4>
            <div class="left col-xs-8">
                <p>{{ $elemento->cliteral}} {{$elemento->idGrupo}}</p>
                <ul class="list-unstyled">
                    @if (isset(AuthUser()->codigo))
                    <li><i class="fa fa-phone"></i>@if ($elemento->mostrar) {{$elemento->movil1}} @else -oculto- @endif</li>
                    @if ($elemento->mostrar)<li><i class="fa fa-phone"></i> {{$elemento->movil2}} </li>@endif
                    @endif
                    <li><i class="fa fa-envelope"></i> {{$elemento->email}}</li>
                </ul>
            </div>
            <div class="right col-xs-4 text-center">
                <img src="{{ asset('storage/'.$elemento->foto) }}" alt="" class="img-circle img-responsive">
            </div>
        </div>
        <div class="col-xs-12 bottom text-center">
            <div class="col-xs-12 col-sm-1 emphasis">
                <p class="ratings">
                    @if (estaDentro($elemento->dni))
                    {!! Html::image('img/clock-icon.png' ,'reloj',array('class' => 'iconopequeno', 'id' => 'imgFitxar')) !!}
                    @else
                    {!! Html::image('img/clock-icon-rojo.png' ,'reloj',array('class' => 'iconopequeno', 'id' => 'imgFitxar')) !!}
                    @endif
                </p>
            </div>
            <div class="col-xs-12 col-sm-11 emphasis">
                @include ('intranet.partials.buttons',['tipo' => 'profile'])
            </div>
        </div>
    </div>
</div>
@endforeach