<div id="field_{{ $id }}"{!! Html::classes(['form-group', 'is-invalid' => $hasErrors]) !!}>

<label for="{{ $id }}" class="control-label col-lg-3 col-md-3 col-sm-4 col-xs-4" style="margin-left: 5px;">
        {{ $label }} :
    </label>

{!! $input !!}

@if ( ! empty($errors))
    <div class="controls">
        @foreach ($errors as $error)
            <div class="invalid-feedback d-block">{{ $error }}</div>
        @endforeach
    </div>
@endif

<hr>
</div>
