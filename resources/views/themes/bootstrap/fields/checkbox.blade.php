<div id="field_{{ $id }}"{!! Html::classes(['form-group','item', 'error' => $hasErrors, 'is-invalid' => $hasErrors]) !!}>
    <label for="{{ $id }}" class="control-label col-md-3 col-sm-3 col-xs-12" style="margin-left: 5px;">
        {{ $label }}
        @if ($required) <span class="required">*</span> @endif
    </label>
    <div class='col-md-6 col-sm-6 col-xs-12'>
        {!! $input !!}
    </div>
        @if (!empty($errors))
            <div class="controls">
                @foreach ($errors as $error)
                <div class="invalid-feedback d-block">{{ $error }}</div>
                @endforeach
            </div>
        @endif
</div>
