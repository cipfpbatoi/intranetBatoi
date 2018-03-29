@php $faltanT = $panel->getElementos($pestana)->sortBy('grupo')->groupBy('grupo'); $trimestre = $pestana->getFiltro()[1]; @endphp
<div>
    @if (isset($informes[$trimestre]))
                <strong>{{trans('models.Resultado.generado')}} </strong>
                <a href="/resultado/infTrimestral/{{$informes[$trimestre]}}" class="btn btn-info btn-sm"/><i class='fa fa-folder'></i> {{trans('messages.buttons.show')}}</a> 
                @if ($trimestre == evaluacion()-1)
                    <a href='#' class="btn btn-success btn-sm" id='generar'><i class='fa fa-pencil'></i> {{trans('messages.buttons.edit')}}</a>
                @endif
    @else            
        @if ($faltanT->count() == 0)
            <p>
                    <strong>{{trans('models.Resultado.estan')}} </strong>
                    <a href='#' class="btn btn-success btn-sm" id='generar'><i class='fa fa-pencil'></i> {{trans('messages.buttons.create')}}</a>
            </p>
        @else
            <div class="col-md-12 col-lg-12">
                <p><strong>{{trans('models.Resultado.faltan')}}</strong> <a href="/resultado/aviso/{{$trimestre}}" class="btn btn-danger btn-sm"/>!! {{trans('models.Resultado.avisa')}}  !!</a><p/>
                <ul>
                    @foreach ($faltanT as $grupo) 
                        <li><strong>{{$grupo->first()->grupo}}</strong>
                            <ul>
                                @foreach ($grupo as $elemento)
                                    <li>{{$elemento->modulo}} ( {{$elemento->nombre}} )</li>
                                @endforeach 
                            </ul>
                        </li>
                    @endforeach  
               </ul>
            </div>    
        @endif
    @endif    
</div>
