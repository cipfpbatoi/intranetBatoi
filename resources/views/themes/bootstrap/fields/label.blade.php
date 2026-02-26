@php
$inputHtml = is_object($input) && method_exists($input, '__toString') ? $input->__toString() : (string) $input;
if (strpos($inputHtml,'>')){
    $cadena = substr($inputHtml,strpos($inputHtml,'>')+1,strlen($inputHtml));
    $value = substr($cadena,0,strpos($cadena,'<'));
}
else $value = '';
if (isset($class)) $clase = "form-control has-feedback-left $class"; 
else $clase = 'form-control has-feedback-left';
@endphp
<div id="field_{{ $id }}" {!! Html::classes(['form-group','item','has-error' => $hasErrors,'has-feedback']) !!}>
     <label for="{{ $id }}" class="control-label col-md-3 col-sm-3 col-xs-12" style="margin-left: 5px;">
        {{ $label }}
    </label>
    <div class='col-md-6 col-sm-6 col-xs-12'>
        @switch(substr($value,-3))
            @case('jpg')
            @case('gif')
            @case('png')
            @case('peg')
                <img src='/storage/fotos/{{$value}}?v={{time()}}' width="60" height="80" />{{$input}}
                @break
            @default 
                {{ $input }}
        @endswitch
    </div>
</div>
