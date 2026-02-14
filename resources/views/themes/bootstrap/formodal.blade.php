<div class="modal-body">
    {!! Form::model($elemento,['class'=>'form-horizontal form-label-left','enctype'=>"multipart/form-data"]) !!}
    {{ csrf_field() }}
    <input id='metodo' type='hidden' name='_method' value='{{old('_method')}}'/>
    <input id='id' type='hidden' name='id' value='{{old('id')}}'/>
    @foreach($fillable as $property)
        @php
            $tipo = $default[$property]['type'];
            $params = $default[$property]['params'] ?? [];
            $value = $default[$property]['default'];
        @endphp

        @switch($tipo)
            @case('file')
                <x-form.file-input
                        :name="$property"
                        :label="$params['label'] ?? __('validation.attributes.' . $property)"
                        :current-file="$elemento->$property"
                        :params="$params"
                />
                @break

            @case('date')
            @case('datetime')
            @case('time')
                <x-form.generic-field
                        :name="$property"
                        type="text"
                        :value="$value"
                        :params="$params"
                />
                @break

            @default
                <x-form.generic-field
                        :name="$property"
                        :type="$tipo"
                        :value="$value"
                        :params="$params"
                />
        @endswitch

    @endforeach
</div>
<div class="modal-footer">
    <button id='close' class="btn btn-danger" data-dismiss="modal" value='' />@lang("messages.buttons.cancel")</button>
    {!! Form::submit(trans('messages.buttons.submit'),['class'=>'btn btn-success','id'=>'submit']) !!}
    {!! Form::close() !!}
    <x-ui.errors />
</div>
