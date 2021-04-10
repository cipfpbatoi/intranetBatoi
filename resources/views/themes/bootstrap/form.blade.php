<div class='form_box'>
    {!! Form::model($elemento,['class'=>'form-horizontal form-label-left','enctype'=>"multipart/form-data"]) !!}
    {{ method_field($method) }}
    @foreach($fillable as $property)
        @php $tipo = $default[$property]['type']; @endphp
        @if ($tipo == 'file')
            {!! Field::label('Fichero Actual',$elemento->$property) !!}
            {!! Field::$tipo($property,$default[$property]['params'],['value'=>$elemento->$property]) !!}
        @else
            @if ($tipo == 'tag')
                <div class="control-group">
                    <label class="control-label col-md-3 col-sm-3 col-xs-12">@lang('validation.attributes.tags')</label>
                    <div class="col-md-9 col-sm-9 col-xs-12">
                        <input name='{{$property}}' id="tags_1" type="text" class="tags form-control" value="{{$elemento->$property}}"/>
                    </div>
                </div>
            @else
                {!! Field::$tipo($property,$default[$property]['default'],$default[$property]['params']) !!}
                @if (isset($default[$property]['params']['disabled']) && $default[$property]['params']['disabled'] == 'disabled')
                    {!! Field::hidden($property,null,[]) !!}
                @endif
            @endif
        @endif
    @endforeach
    @yield('after')
    <a href="{{URL::previous()}}" class="btn btn-danger">@lang("messages.buttons.cancel") </a>
    {!! Form::submit(trans('messages.buttons.submit'),['class'=>'btn btn-success','id'=>'submit']) !!}
    {!! Form::close() !!}
</div>
@include('layouts.partials.error')

