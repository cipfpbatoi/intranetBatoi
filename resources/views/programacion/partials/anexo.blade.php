<div class="x_content">
    <p>
    <b>{{ $elemento->Modulo->literal }} - {{ $elemento->ciclo }}</b><br/>
    {{ $elemento->Modulo->Ciclo->Departamento->literal }}</p>
    Ficheros : <a href="/programacion/{{$elemento->id}}/document" target="_blank"><i class='fa fa-file-pdf-o'></i> {{trans('models.modelos.Programacion')}}</a>
    @for ($i=1;$i<=$elemento->anexos;$i++)
    <a href="/programacion/{{$elemento->id}}/veranexo/{{$i}}" target="_blank"><i class='fa fa-file-pdf-o'></i> {{ trans('messages.buttons.anexo')}}{{$i}}</a></span>
    @endfor
    <br/>Estado : {{ trans('messages.situations.' . $elemento->estado) }}<br/>
    @if ($elemento->estado < 3)
    {!! Form::open(['route' => ['programacion.storeanexo',$elemento->id],'class'=>'form-horizontal form-label-left','enctype'=>"multipart/form-data"]) !!}
        {{ csrf_field() }}
        {!! Field::File('Anexo') !!} 
        <a href="/programacion/{{$elemento->id}}/deleteanexo" class="btn btn-danger">{{trans('messages.buttons.delete')}} </a>
        {!! Form::submit(trans('messages.buttons.submit'),['class'=>'btn btn-success','id'=>'submit']) !!}
        <a href='/programacion' class="btn btn-primary">{{trans('messages.buttons.atras')}}</a>
    {!! Form::close() !!}
    
    </div>
    @endif
</div>
