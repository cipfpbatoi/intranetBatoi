<div class="container col-lg-12" style="font-size: x-small;" >
    <hr/>
    @php($documento = config('variables.pdf.'.$document))
    @if ($documento!= null)
        <div style="float:right;width:20%;margin-bottom: 30px;margin-left: 60px"  >
           Codi: {{ $documento['codi'] }}  - Núm. edició: {{ $documento['edicio'] }}
        </div>
    @endif
</div>
