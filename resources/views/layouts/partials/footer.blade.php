<!-- footer content -->
<footer>
    <div class="salto">
        <a href="{{ config('constants.contacto.web')}}" target="_blank"><strong>{{ config('constants.contacto.nombre')}}</strong></a><strong> - @lang("messages.generic.direccion"):</strong> {{config('constants.contacto.direccion')}}, {{ config('constants.contacto.postal')}} {{ config('constants.contacto.poblacion')}},{{ config('constants.contacto.provincia')}}
        <a href="{{ config('constants.contacto.mapa')}}" target="_blank"> {!! Html::image('img/direccion.png' ,'direccion',array('class' => 'iconopequeno')) !!}</a>
    </div><br/>
    <div class="salto">
        {!! Html::image('img/telefono.png' ,'telefono',array('class' => 'iconopequeno')) !!}<strong>{{ trans('messages.generic.telefono')}}: </strong>{{ config('constants.contacto.telefono')}}<strong> - Fax:</strong> {{ config('constants.contacto.fax')}}<strong>- email:</strong> {{ config('constants.contacto.email')}}
    </div><br/>
    <div class="salto">
        <strong>@lang("messages.generic.secretaria"): </strong>@lang("messages.generic.horario")
    </div><br/>			
    <div class="salto">
        <a href="legal" target="_blank"><strong>@lang("messages.generic.aviso")</strong></a>
    </div>
    <div class="clearfix"></div>
</footer>
<!-- /footer content -->