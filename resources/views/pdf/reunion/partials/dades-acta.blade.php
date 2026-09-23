<div class="container col-lg-12">
    Acta número <strong>{{ $datosInforme->numero }}</strong>
    curs <strong>{{ $datosInforme->curso }}</strong>
    del dia <strong>{{ $datosInforme->dia }}</strong>
    a les <strong>{{ $datosInforme->hora }}</strong>
    @if ($datosInforme->llocReunio)
        al lloc <strong>{{ $datosInforme->llocReunio }}</strong>
    @endif
</div>
