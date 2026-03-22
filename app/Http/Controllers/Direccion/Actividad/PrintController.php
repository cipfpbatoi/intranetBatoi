<?php

namespace Intranet\Http\Controllers\Direccion\Actividad;

use Intranet\Entities\Actividad;
use Intranet\Http\Controllers\Controller;
use Intranet\Http\Traits\Autorizacion;

/**
 * Impressió massiva d'activitats autoritzades des del panell de Direcció.
 */
class PrintController extends Controller
{
    use Autorizacion;

    /**
     * @var string
     */
    protected $class = Actividad::class;

    /**
     * @var string
     */
    protected $model = 'Actividad';

    /**
     * Genera l'informe PDF d'activitats autoritzades.
     *
     * @return mixed
     */
    public function __invoke()
    {
        $this->authorize('create', Actividad::class);

        return $this->imprimir('extraescolars');
    }
}
