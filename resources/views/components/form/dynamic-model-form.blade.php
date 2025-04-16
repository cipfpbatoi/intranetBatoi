@props([
    'model',          // Model vinculat (ex: $elemento)
    'method' => 'POST',
    'fillable' => [],
    'defaults' => [],
])

<div class='form_box'>
    {!! Form::model($model, ['class' => 'form-horizontal form-label-left', 'enctype' => 'multipart/form-data']) !!}
    {{ method_field($method) }}
    @foreach($fillable as $property)
        <x-form.dynamic-field-renderer
                :name="$property"
                :type="$defaults[$property]['type'] ?? 'text'"
                :label="$defaults[$property]['label'] ?? __('validation.attributes.' . $property)"
                :value="$defaults[$property]['default']"
                :params="$defaults[$property]['params'] ?? []"
                :current-file="$model->$property"
        />
    @endforeach

    {{ $slot }}

    <a href="{{ URL::previous() }}" class="btn btn-danger">
        @lang("messages.buttons.cancel")
    </a>

    {!! Form::submit(trans('messages.buttons.submit'), ['class' => 'btn btn-success', 'id' => 'submit']) !!}
    {!! Form::close() !!}
</div>

<x-ui.errors />
