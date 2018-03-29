@foreach ($panel->getElementos($pestana) as $elemento)
<div class="col-md-4 col-sm-4 col-xs-12 profile_details">
    <div id="{{$elemento->aula}}" class="well profile_view">
        <div class="col-sm-12">
            <h4 class="brief">
                <i class="fa fa-pencil"></i> {{$elemento->aula}} ({{ $elemento->descripcion }}) 
            </h4>
            <div class="left col-xs-12">
                <h5>DEP. {{$elemento->Departamento->literal}}
                </h5>
                <ul class="list-unstyled">
                    @if ($elemento->gMati != '')
                        <li><i class="fa fa-group"></i> {{$elemento->GruposMati->nombre}} </li>
                    @endif
                    @if ($elemento->gVesprada != '')
                        <li><i class="fa fa-group"></i> {{$elemento->GruposVesprada->nombre}} </li>
                    @endif
                </ul>
            </div>
        </div>
        <div class="col-xs-12 bottom text-center">
            <div class="col-xs-12 col-sm-2 emphasis">
                <p class="ratings">
<!--                    @if ($elemento->estado<2) <a href='#' class='btn btn-danger btn-xs' >
                    @else <a href='#' class='btn btn-success btn-xs' >   
                    @endif    
                    {{ $elemento->situacion }}</a>-->
                </p>
            </div>
            <div class="col-xs-12 col-sm-9 emphasis">
                @include ('intranet.partials.buttons',['tipo' => 'profile'])
            </div>
        </div>
    </div>
</div>
@endforeach
