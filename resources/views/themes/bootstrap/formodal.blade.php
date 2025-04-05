<div class="modal-body">
    {!! Form::model($elemento,['class'=>'form-horizontal form-label-left','enctype'=>"multipart/form-data"]) !!}
    {{ csrf_field() }}
    <input id='metodo' type='hidden' name='_method' value='{{old('_method')}}'/>
    <input id='id' type='hidden' name='id' value='{{old('id')}}'/>

    @foreach($fillable as $property)
        @php $tipo = $default[$property]['type']; @endphp
        @if ($tipo == 'file')
            {!! Field::label('Fichero Actual',$elemento->$property) !!}
            {!! Field::$tipo($property,$default[$property]['params']) !!}
        @else
            @if (($tipo == 'date') || ($tipo == 'datetime') || ($tipo == 'time') || ($tipo == 'hora'))
                {!! Field::text($property,$default[$property]['default'],$default[$property]['params']) !!}
            @else
                {!! Field::$tipo($property,$default[$property]['default'],$default[$property]['params']) !!}
                @if (isset($default[$property]['params']['disabled']))
                    {!! Field::hidden($property,null,[]) !!}
                @endif
            @endif
        @endif
    @endforeach
</div>
<div class="modal-footer">
    <button id='close' class="btn btn-danger" data-dismiss="modal" value='' />@lang("messages.buttons.cancel")</button>
    {!! Form::submit(trans('messages.buttons.submit'),['class'=>'btn btn-success','id'=>'submit']) !!}
    {!! Form::close() !!}
    <x-ui.errors />

</div>

