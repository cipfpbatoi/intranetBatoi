<footer>
    {{-- Adreça del centre --}}
    <div class="salto">
        <a href="{{ config('contacto.web') }}" target="_blank">
            <strong>{{ config('contacto.nombre') }}</strong>
        </a>
        <strong> - @lang("messages.generic.direccion"):</strong>
        {{ config('contacto.direccion') }},
        {{ config('contacto.postal') }}
        {{ config('contacto.poblacion') }},
        {{ config('contacto.provincia') }}

        <a href="{{ config('contacto.mapa') }}" target="_blank">
            {!! Html::image('img/direccion.png', 'direccion', ['class' => 'iconopequeno']) !!}
        </a>
    </div><br/>

    {{-- Telèfon, fax i email --}}
    <div class="salto">
        {!! Html::image('img/telefono.png', 'telefono', ['class' => 'iconopequeno']) !!}
        <strong>{{ trans('messages.generic.telefono') }}:</strong> {{ config('contacto.telefono') }}
        <strong> - Fax:</strong> {{ config('contacto.fax') }}
        <strong> - Email:</strong> {{ config('contacto.email') }}
    </div><br/>

    {{-- Secretaria --}}
    <div class="salto">
        <strong>@lang("messages.generic.secretaria"):</strong> @lang("messages.generic.horario")
    </div><br/>

    {{-- Avís legal --}}
    <div class="salto">
        <a href="{{ url('legal') }}" target="_blank">
            <strong>@lang("messages.generic.aviso")</strong>
        </a>
    </div>

    <div class="clearfix"></div>
</footer>
