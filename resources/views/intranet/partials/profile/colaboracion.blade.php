@foreach ($panel->getElementos($pestana) as $elemento)
    <div class="col-md-4 col-sm-4 col-xs-12 profile_details">
        <div id="{{$elemento->id}}" class="well profile_view">
            <div class="col-sm-12">
                <div class="left col-xs-12">
                    <h5>{{$elemento->puestos}} {{$elemento->Centro->nombre}}</h5>
                    <ul class="list-unstyled">
                        <li>{{$elemento->contacto}}</li>
                        <li>{{$elemento->telefono}}</li>
                        <li>{{$elemento->email}}</li>
                    </ul>
                </div>
            </div>
            <div class="col-xs-12 bottom text-center">
                <div class="col-xs-12 col-sm-4 emphasis">
                    <p class="ratings">
                        {{$elemento->Centro->localidad}}
                    </p>
                </div>
                <div class="col-xs-12 col-sm-8 emphasis">
                    @include ('intranet.partials.buttons',['tipo' => 'profile'])
                </div>
            </div>
        </div>
    </div>
@endforeach
