@php
$inputHtml = is_object($input) && method_exists($input, '__toString') ? $input->__toString() : (string) $input;
if (strpos($inputHtml,'value')){
    $cadena = substr($inputHtml,strpos($inputHtml,'value')+7,strlen($inputHtml));
    $value = substr($cadena,0,strpos($cadena,'"'));
}
else $value = '';
if (isset($class)) $clase = "form-control $class";
else $clase = 'form-control';
@endphp
<div id="field_{{ $id }}" {!! Html::classes(['form-group','item','is-invalid' => $hasErrors]) !!}>
     <label for="{{ $id }}" class="control-label col-md-3 col-sm-3 col-xs-12" style="margin-left: 5px;">
        {{ $label }}
        @if ($required) <span class="required">*</span>@endif
    </label>
    <div class='col-md-6 col-sm-6 col-xs-12'>
        <input name='{{$htmlName}}'  @if ($required) required @endif type='text' class='{{$clase}}' id='{{ $id }}' placeholder="{!! $label !!}" value='{{ $value }}'>
        <span class='fa {{$fa}} form-control-feedback' aria-hidden='true'></span>
    </div>
</div>
@foreach ($errors as $error)
<div class="invalid-feedback d-block">{{ $error }}</div>
@endforeach
