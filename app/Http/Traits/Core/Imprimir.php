<?php

namespace Intranet\Http\Traits\Core;

use Illuminate\Support\Facades\Response;
use Intranet\Services\Document\PdfService;
use Intranet\Services\Notifications\AdviseTeacher;
use Intranet\Services\Calendar\CalendarService;
use Intranet\Services\General\GestorService;


/**
 * Trait de suport per a funcionalitats d'impressió i calendari en controllers.
 *
 * Contracte esperat del controller que usa el trait:
 * - `protected string $class`: FQCN del model (ex. `Intranet\Entities\Reunion`).
 */
trait Imprimir
{
    /**
     * Envia notificació de recordatori al professor responsable del registre.
     *
     * @param int|string $id Identificador del registre.
     * @return \Illuminate\Http\RedirectResponse
     */
    protected function notify($id)
    {
        $this->guardPrintableContract();
        AdviseTeacher::exec($this->class::findOrFail($id));
        return back();
    }

    /**
     * Fa de façana del servei de PDF per mantindre compatibilitat als controllers.
     *
     * @param string $informe Vista Blade de l'informe.
     * @param mixed $todos Dades a injectar en la vista.
     * @param mixed $datosInforme Dades auxiliars per al peu/capçalera.
     * @param string $orientacion Orientació del PDF.
     * @param string|array $dimensiones Mida del paper.
     * @param int $margin_top Marge superior.
     * @return mixed
     */
    protected static function hazPdf($informe, $todos, $datosInforme = null, $orientacion = 'portrait', $dimensiones = 'a4',
                                     $margin_top= 15)
    {
        return app(PdfService::class)->hazPdf($informe, $todos, $datosInforme , $orientacion , $dimensiones , $margin_top );
    }

    /**
     * Genera la resposta iCalendar d'un registre.
     *
     * @param int|string $id Identificador del registre.
     * @param string $descripcion Nom del camp de descripció.
     * @param string $objetivos Nom del camp d'objectius.
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function ics($id, $descripcion='descripcion', $objetivos='objetivos')
    {
        $this->guardPrintableContract();
        $elemento = $this->class::findOrFail($id);

        if (!isset($elemento->$descripcion) || !isset($elemento->$objetivos)) {
            return back()->with(
                'error',
                "No existeixen els camps '$descripcion' i/o '$objetivos' per generar l'arxiu iCalendar."
            );
        }

        $vCalendar = CalendarService::build($elemento,$descripcion,$objetivos);
        return Response::view('ics', compact('vCalendar'))->header('Content-Type', 'text/calendar');
    }

    /**
     * Mostra o descarrega el document vinculat al registre en el gestor documental.
     *
     * @param int|string $id Identificador del registre.
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector|\Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function gestor($id)
    {
        $this->guardPrintableContract();
        return (new GestorService($this->class::findOrFail($id)))->render();
    }

    /**
     * Valida el contracte mínim requerit pel trait.
     */
    private function guardPrintableContract(): void
    {
        if (!isset($this->class)) {
            abort(500, "L'atribut 'class' no està definit en la classe que usa el trait Imprimir.");
        }
    }

}
