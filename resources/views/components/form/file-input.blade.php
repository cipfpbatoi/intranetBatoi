@props([
    'name',
    'label' => null,
    'currentFile' => null,
    'params' => [],
])

@php
    $attributes = $params;
    if ($label) {
        $attributes['label'] = $label;
    }
    $isDisabled = !empty($attributes['disabled']) && $attributes['disabled'] === 'disabled';
    $isRequired = in_array('required', $attributes, true) || (!empty($attributes['required']) && $attributes['required'] !== 'off');
    $fieldId = $attributes['id'] ?? $name;
@endphp

{!! Field::file($name, $attributes) !!}

@if ($currentFile)
    <div class="col-md-offset-3 col-md-6 col-sm-6 col-xs-12" style="margin-bottom: 10px;">
        <p class="form-control-static">
            <a href="{{ asset('storage/' . $currentFile) }}" target="_blank" rel="noopener">
                {{ basename($currentFile) }}
            </a>
        </p>
    </div>
@endif

@if ($isDisabled)
    {!! Field::hidden($name, null, []) !!}
@endif
