<div class='form_box'>
    {!! Form::model($elemento,['class'=>'form-horizontal form-label-left','enctype'=>"multipart/form-data"]) !!}
        {{ method_field($method) }}
        @foreach($fillable as $property)
            <x-form.dynamic-field-renderer
                    :name="$property"
                    :type="$default[$property]['type']"
                    :label="'Fitxer Actual'"
                    :value="$default[$property]['default']"
                    :params="$default[$property]['params'] ?? []"
                    :current-file="$elemento->$property"
            />
        @endforeach
        @yield('after')
        <a href="{{URL::previous()}}" class="btn btn-danger">@lang("messages.buttons.cancel") </a>
        {!! Form::submit(trans('messages.buttons.submit'),['class'=>'btn btn-success','id'=>'submit']) !!}
    {!! Form::close() !!}
</div>
<x-ui.errors />
