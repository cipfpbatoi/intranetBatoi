@foreach ($panel->getElementos($pestana) as $elemento)
<div class="col-md-4 col-sm-4 col-xs-12 profile_details">
    <div id="{{$elemento->nia}}" class="well profile_view">
        <div class="col-sm-12">
            <h4 class="brief"><i>{{ $elemento->nia}}</i></h4>
            <div class="left col-xs-7">
                <h2>{{ $elemento->nombre }} {{ $elemento->apellido1}} {{$elemento->apellido2}}</h2>
                <p><strong>{{ trans("validation.attributes.expediente") }}</strong> {{$elemento->expediente}} </p>
                <ul class="list-unstyled">
                    <li><i class="fa fa-phone"></i> {{$elemento->telef1}}</li>
                    @if ($elemento->telef2 != " ")
                    <li><i class="fa fa-phone"></i> {{$elemento->telef2}}</li>
                    @endif
                </ul>
            </div>
            <div class="right col-xs-5 text-center">
                <img src="{{asset('storage/'.$elemento->foto)}}" alt="" heigth="90px" width="70px" class="img-circle img-responsive">
            </div>
            <div class="left col-xs-12">
                <ul class="list-unstyled">
                    <li><i class="fa fa-building"></i> {{$elemento->domicilio}}</li>
                    @if ($elemento->email != " ")
                    <li><i class="fa fa-envelope"></i> {{$elemento->email}}</li>
                    @endif
                </ul>
            </div>
        </div>
        
        <div class="col-xs-12 bottom text-center">
            <div class="col-xs-12 col-sm-6 emphasis">
                <p class="ratings">
                    @php  $nac = new Jenssegers\Date\Date($elemento->fecha_nac) @endphp
                    <a>{{ $nac->age }} {{ trans("validation.attributes.años") }}</a>
                    @if ($nac->age > 15)
                    <a href="#"><span class="fa fa-star"></span></a>
                    @else
                    <a href="#"><span class="fa fa-star-o"></span></a>
                    @endif
                    @if ($nac->age > 17)
                    <a href="#"><span class="fa fa-star"></span></a>
                    @else
                    <a href="#"><span class="fa fa-star-o"></span></a>
                    @endif
                    @if ($nac->age > 20)
                    <a href="#"><span class="fa fa-star"></span></a>
                    @else
                    <a href="#"><span class="fa fa-star-o"></span></a>
                    @endif
                </p>
            </div>
            <div class="col-xs-12 col-sm-6 emphasis">
                @include ('intranet.partials.components.buttons',['tipo' => 'profile'])
             </div>
        </div>
    </div>
</div>
@endforeach

