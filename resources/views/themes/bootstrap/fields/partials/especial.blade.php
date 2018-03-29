@php
if (strpos($input->__toString(),'value')){
    $cadena = substr($input->__toString(),strpos($input->__toString(),'value')+7,strlen($input->__toString()));
    $value = substr($cadena,0,strpos($cadena,'"'));
}
else $value = '';
if (isset($class)) $clase = "form-control has-feedback-left $class"; 
else $clase = 'form-control has-feedback-left';
@endphp
<div id="field_{{ $id }}" {!! Html::classes(['form-group','item','has-error' => $hasErrors,'has-feedback']) !!}>
     <label for="{{ $id }}" class="control-label col-md-3 col-sm-3 col-xs-12" style="margin-left: 5px;">
        {{ $label }}
        @if ($required) <span class="required">*</span>@endif
    </label>
    <div class='col-md-6 col-sm-6 col-xs-12'>
        <input name='{{$htmlName}}'  @if ($required) required @endif type='text' class='{{$clase}}' id='{{ $id }}' placeholder="{!! $label !!}" value='{{ $value }}'>
        <span class='fa {{$fa}} form-control-feedback left' aria-hidden='true'></span> 
    </div>
</div>
@foreach ($errors as $error)
<p class="help-block">{{ $error }}</p>
@endforeach
