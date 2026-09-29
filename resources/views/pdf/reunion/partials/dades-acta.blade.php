<div class="container col-lg-12">
    @include('pdf.reunion.partials.numero-acta')
    curs <strong>{{ $datosInforme->curso }}</strong>
    del dia <strong>{{ $datosInforme->dia }}</strong>
    a les <strong>{{ $datosInforme->hora }}</strong>
    @if ($datosInforme->llocReunio)
        al lloc <strong>{{ $datosInforme->llocReunio }}</strong>
    @endif
</div>
