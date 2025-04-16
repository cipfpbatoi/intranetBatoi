<div class="container col-lg-12" style="font-size: x-small;clear: both" >
    <hr/>
    @php($documento = config('footers.'.$document))
    @if ($documento!= null)
        <div style="float:right;width:20%;margin-bottom: 30px;margin-left: 60px"  >
           Codi: {{ $documento['codi'] }}  - Núm. edició: {{ $documento['edicio'] }}
        </div>
    @endif
</div>
