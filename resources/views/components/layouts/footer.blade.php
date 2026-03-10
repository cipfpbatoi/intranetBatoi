<footer class="app-footer">
    {{-- Línia 1: telèfon i email --}}
    <div class="app-footer__line">
        <a href="{{ config('contacto.web') }}" target="_blank">
            <strong>{{ config('contacto.nombre') }}</strong>
        </a><img src="{{ asset('img/direccion.png') }}" alt="direccion" class="iconopequeno app-footer__icon">
        {{ config('contacto.direccion') }}, {{ config('contacto.postal') }} {{ config('contacto.poblacion') }}, {{ config('contacto.provincia') }}
        <img src="{{ asset('img/telefono.png') }}" alt="telefono" class="iconopequeno app-footer__icon">
        {{ config('contacto.telefono') }}
        <i class="fa fa-fax app-footer__icon" aria-hidden="true"></i>
        {{ config('contacto.fax') }}

        <i class="fa fa-envelope app-footer__icon" aria-hidden="true"></i>
        {{ config('contacto.email') }}
    </div>

    {{-- Línia 2: resta de dades --}}
    <div class="app-footer__line">
        <strong> @lang("messages.generic.secretaria"):</strong> @lang("messages.generic.horario")
        <strong> - </strong>
        <a href="{{ url('legal') }}" target="_blank">
            <strong>@lang("messages.generic.aviso")</strong>
        </a>
    </div>

    <div class="clearfix"></div>
</footer>

<style>
    .app-footer {
        padding: 18px 22px;
        color: #6b7f99;
        font-size: 14px;
        line-height: 1.7;
        border-top: 1px solid #e6edf5;
        background: #fff;
    }

    .app-footer strong {
        color: #556d8b;
    }

    .app-footer a {
        color: #556d8b;
    }

    .app-footer__line {
        margin-bottom: 6px;
    }

    .app-footer__line:last-child {
        margin-bottom: 0;
    }

    .app-footer__icon {
        margin-right: 6px;
        margin-left: 10px;
        vertical-align: middle;
    }

    .app-footer__line .app-footer__icon:first-child {
        margin-left: 0;
    }

    @media (min-width: 992px) {
        .app-footer__line {
            white-space: nowrap;
        }
    }

    @media (max-width: 768px) {
        .app-footer {
            font-size: 13px;
            line-height: 1.6;
            padding: 14px 16px;
        }

        .app-footer__line {
            white-space: normal;
        }
    }
</style>
