@foreach ($panel->getElementos($pestana) as $elemento)
<div class="col-md-4 col-sm-4 col-xs-12 profile_details">
    <div id="{{$elemento->idModulo}}" class="well profile_view">
        <div class="col-sm-12">
            <h4 class="brief">
                 {{ $elemento->departamento }}<br/>
                 {{ trans('messages.buttons.ciclo')}}: {{ $elemento->ciclo }}<br/>
                 {{ trans('messages.buttons.modulo')}}: {{ $elemento->Xmodulo }}<br/>
                 {{ trans('validation.attributes.Profesor')}}: {{ $elemento->Profesor->ShortName }}<br/>
            </h4>
            <div class="left col-xs-6">
                @if ($elemento->fichero)
                    <h5><i class="fa fa-file-pdf-o"></i> <a href="/programacion/{{$elemento->id}}/document" target="_blank">  {!! trans('models.modelos.Programacion') !!}</a></h5>
                @endif    
                @for ($i=1;$i<=$elemento->anexos;$i++)
                <i class="fa fa-file-pdf-o"></i> <a href="/programacion/{{$elemento->id}}/veranexo/{{$i}}" target="_blank">{{ trans('messages.buttons.anexo') }}{{$i}}</a><br/>
                @endfor
             </div>
            <div class="left col-xs-6">
                <h5>{{trans('messages.generic.validez')}}</h5>
                <i class="fa fa-calendar"></i> {{ $elemento->desde }}<br/>
                <i class="fa fa-calendar"></i> {{ $elemento->hasta }}
            </div>
            <div class="left col-xs-12">
                <h6>{{ trans('messages.buttons.checkList') }}</h6>
                {!! Form::open(['route' => ['programacion.checklist',$elemento->id],'class'=>'form-horizontal form-label-left']) !!}
                {{ csrf_field() }}
                {!! Form::checkboxes('items',$panel->items,checkItems($elemento->checkList)) !!} 
            </div>
        </div>
        <div class="col-xs-12 bottom text-center">
            <div class="col-xs-12 col-sm-2 emphasis">
                @if ($elemento->estado<2) <a href='#' class='btn btn-danger btn-xs' >
                    @else <a href='#' class='btn btn-success btn-xs' >   
                    @endif    
                    {{ $elemento->situacion }}</a>
            </div>
            <div class="col-xs-12 col-sm-9 emphasis">
                 @include ('intranet.partials.buttons',['tipo' => 'profile'])
                {!! Form::close() !!}
            </div>
        </div>
    </div>
</div>
@endforeach