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
            $inputClass = (string) ($params['class'] ?? '');
            $renderType = $tipo;

            if (str_contains($inputClass, ' datetime')) {
                $renderType = 'datetimeLocal';
                $value = is_string($value) ? preg_replace('/^(\d{4}-\d{2}-\d{2}) (\d{2}:\d{2})$/', '$1T$2', $value) : $value;
                unset($params['template']);
            } elseif (str_contains($inputClass, ' date')) {
                $renderType = 'date';
                unset($params['template']);
            } elseif (str_contains($inputClass, ' time')) {
                $renderType = 'time';
                unset($params['template']);
            }
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

            @default
                <x-form.generic-field
                        :name="$property"
                        :type="$renderType"
                        :value="$value"
                        :params="$params"
                />
        @endswitch

    @endforeach
</div>
<div class="modal-footer">
    <button id="close" type="button" class="btn btn-danger" data-bs-dismiss="modal">@lang("messages.buttons.cancel")</button>
    {!! Form::submit(__('messages.buttons.submit'),['class'=>'btn btn-success','id'=>'submit']) !!}
    {!! Form::close() !!}
    <x-ui.errors />
</div>
