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

<div id="field_{{ $fieldId }}" class="form-group item">
    <label for="{{ $fieldId }}" class="control-label col-md-3 col-sm-3 col-xs-12" style="margin-left: 5px;">
        {{ $label ?? __('validation.attributes.' . $name) }}
        @if ($isRequired)
            <span class="required">*</span>
        @endif
    </label>
    <div class="col-md-6 col-sm-6 col-xs-12">
        @if ($currentFile)
            <p class="form-control-static">
                <a href="{{ asset('storage/' . $currentFile) }}" target="_blank" rel="noopener">
                    {{ basename($currentFile) }}
                </a>
            </p>
        @endif

        {!! Field::file($name, $attributes) !!}

        @if ($isDisabled)
            {!! Field::hidden($name, null, []) !!}
        @endif
    </div>
</div>
